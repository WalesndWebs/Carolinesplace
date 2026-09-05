<?php
/**
 * Caroline's Place — Spa Menu & Booking / Review Flow
 */
require_once __DIR__ . '/api/db.php';

$db = getDb();

// ─────────────────────────────────────────────────────────────
// IF POST WITH SELECTED SERVICES: RENDER REVIEW PAGE
// ─────────────────────────────────────────────────────────────
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !empty($_POST['svc_selected'])) {
    $pageTitle = "Review Your Spa Booking — Caroline's Place";
    $pageDesc  = "Review your selected spa services, date and time, and confirm your reservation.";
    $current   = 'spa_menu';

    $selectedIds = $_POST['svc_selected'];
    if (!is_array($selectedIds)) $selectedIds = [$selectedIds];

    $selectedOptions = $_POST['svc_option'] ?? [];
    $selectedQtys    = $_POST['svc_qty'] ?? [];

    $lines = [];
    $total = 0.0;

    $svcStmt = $db->prepare("SELECT id, name, description FROM services WHERE id = ?");
    $optStmt = $db->prepare("SELECT id, option_label, price_ngn FROM options WHERE id = ?");
    $firstOptStmt = $db->prepare("SELECT id, option_label, price_ngn FROM options WHERE service_id = ? ORDER BY sort_order ASC LIMIT 1");

    foreach ($selectedIds as $rawSid) {
        $sid = (int)$rawSid;
        if ($sid <= 0) continue;

        $svcStmt->execute([$sid]);
        $svc = $svcStmt->fetch();
        if (!$svc) continue;

        $chosenOptId = isset($selectedOptions[$sid]) ? (int)$selectedOptions[$sid] : null;
        $opt = null;
        if ($chosenOptId) {
            $optStmt->execute([$chosenOptId]);
            $opt = $optStmt->fetch();
        }
        if (!$opt) {
            $firstOptStmt->execute([$sid]);
            $opt = $firstOptStmt->fetch();
        }

        $unitPrice = $opt ? (float)$opt['price_ngn'] : 0.0;
        $qty = isset($selectedQtys[$sid]) ? max(1, min(10, (int)$selectedQtys[$sid])) : 1;
        $lineTotal = $unitPrice * $qty;
        $total += $lineTotal;

        $lines[] = [
            'sid'          => $sid,
            'service_name' => $svc['name'],
            'option_id'    => $opt ? $opt['id'] : null,
            'option_label' => $opt ? $opt['option_label'] : 'Standard',
            'unit_price'   => $unitPrice,
            'qty'          => $qty,
            'line_total'   => $lineTotal,
        ];
    }

    if (empty($lines)) {
        header('Location: /spa_menu.php');
        exit;
    }

    $timeSlots = [
        '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
        '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM',
        '05:00 PM', '06:00 PM', '07:00 PM'
    ];

    require_once __DIR__ . '/includes/header.php';
    ?>

    <div class="page-header">
      <span class="page-header__eyebrow">Spa Reservations</span>
      <h1 class="page-header__title">Review Your Spa Booking</h1>
      <p class="page-header__desc">
        Please review your selected services below, then complete your guest details.
      </p>
    </div>

    <section class="section" style="padding-top:0;">
      <div class="container container--md">

        <div class="booking-card" style="max-width:900px; margin:0 auto; background:#fff; border-radius:18px; padding:36px; border:1px solid rgba(27,20,16,0.1); box-shadow:0 8px 30px rgba(17,17,17,0.04);">

          <div style="margin-bottom:40px;">
            <div class="form-section-title" style="font-family:var(--font-serif); font-size:1.3rem; margin-bottom:18px; color:var(--primary); border-bottom:1px solid rgba(27,20,16,0.1); padding-bottom:8px;">Selected Services</div>

            <div style="overflow-x:auto;">
              <table style="width:100%;border-collapse:collapse;font-size:0.88rem;">
                <thead>
                  <tr style="border-bottom:2px solid var(--border);">
                    <th style="text-align:left;padding:14px 10px;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);font-weight:400;">Service</th>
                    <th style="text-align:left;padding:14px 10px;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);font-weight:400;">Option</th>
                    <th style="text-align:right;padding:14px 10px;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);font-weight:400;">Unit</th>
                    <th style="text-align:center;padding:14px 10px;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);font-weight:400;">Qty</th>
                    <th style="text-align:right;padding:14px 10px;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);font-weight:400;">Line Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($lines as $l): ?>
                    <tr style="border-bottom:1px solid rgba(27,20,16,0.08);">
                      <td style="padding:14px 10px;font-weight:500;"><?php echo htmlspecialchars($l['service_name']); ?></td>
                      <td style="padding:14px 10px;color:var(--muted);"><?php echo htmlspecialchars($l['option_label']); ?></td>
                      <td style="padding:14px 10px;text-align:right;font-variant-numeric:tabular-nums;"><?php echo priceFmt($l['unit_price']); ?></td>
                      <td style="padding:14px 10px;text-align:center;font-variant-numeric:tabular-nums;"><?php echo (int)$l['qty']; ?></td>
                      <td style="padding:14px 10px;text-align:right;font-weight:600;font-variant-numeric:tabular-nums;"><?php echo priceFmt($l['line_total']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4" style="padding:20px 10px 10px;text-align:right;font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);">Grand Total</td>
                    <td style="padding:20px 10px 10px;text-align:right;font-family:var(--font-serif);font-size:2.2rem;font-weight:700;color:var(--primary);font-variant-numeric:tabular-nums;"><?php echo priceFmt($total); ?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <form method="POST" action="/api/book_spa.php" novalidate id="bookingForm">

            <?php foreach ($lines as $l): ?>
              <input type="hidden" name="line_service_id[]" value="<?php echo (int)$l['sid']; ?>" />
              <input type="hidden" name="line_option_id[]" value="<?php echo (int)$l['option_id']; ?>" />
              <input type="hidden" name="line_unit[]" value="<?php echo (float)$l['unit_price']; ?>" />
              <input type="hidden" name="line_qty[]" value="<?php echo (int)$l['qty']; ?>" />
            <?php endforeach; ?>
            <input type="hidden" name="total_amount" value="<?php echo (float)$total; ?>" />

            <div style="margin-bottom:40px;">
              <div class="form-section-title" style="font-family:var(--font-serif); font-size:1.3rem; margin-bottom:18px; color:var(--primary); border-bottom:1px solid rgba(27,20,16,0.1); padding-bottom:8px;">Guest Details</div>

              <div class="form-group" style="margin-bottom:16px;">
                <label for="full_name" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-input" style="width:100%; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" placeholder="John Doe" required />
              </div>

              <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group">
                  <label for="email" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Email</label>
                  <input type="email" id="email" name="email" class="form-input" style="width:100%; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" placeholder="john@example.com" required />
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Phone Number</label>
                  <input type="tel" id="phone" name="phone" class="form-input" style="width:100%; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" placeholder="+234..." required />
                </div>
              </div>

              <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group">
                  <label for="preferred_date" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Preferred Date</label>
                  <input type="date" id="preferred_date" name="preferred_date" class="form-input" style="width:100%; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" required />
                </div>
                <div class="form-group">
                  <label for="preferred_time" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Preferred Time</label>
                  <select id="preferred_time" name="preferred_time" class="form-select" style="width:100%; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" required>
                    <option value="">Select a time</option>
                    <?php foreach ($timeSlots as $t): ?>
                      <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="form-group" style="margin-bottom:24px;">
                <label for="notes" class="form-label" style="display:block; margin-bottom:6px; font-weight:500; font-size:14px;">Special Requests / Notes</label>
                <textarea id="notes" name="notes" class="form-textarea" style="width:100%; min-height:90px; padding:12px 14px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:15px;" placeholder="Any specific preferences or requirements..."></textarea>
              </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
              <a href="/spa_menu.php" class="btn btn--outline" style="padding:12px 20px;">← Modify Services</a>
              <button type="submit" class="btn btn--primary" style="padding:14px 28px; font-size:15px; font-weight:600;">
                Confirm Reservation · <?php echo priceFmt($total); ?>
              </button>
            </div>
          </form>

        </div>
      </div>
    </section>

    <script>
    (function() {
      const today = new Date().toISOString().split('T')[0];
      const dateInput = document.getElementById('preferred_date');
      if (dateInput) {
        dateInput.min = today;
      }
    })();
    </script>

    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ─────────────────────────────────────────────────────────────
// DEFAULT: RENDER PUBLIC INTERACTIVE SPA MENU
// ─────────────────────────────────────────────────────────────
$pageTitle = "Spa Menu & Booking — Caroline's Place";
$pageDesc  = "Pick from 100+ luxury spa, hair, nail and body treatments with live running total.";
$current   = 'spa_menu';

// Fetch categories and services
$catStmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$categories = $catStmt->fetchAll();

$svcStmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$services = $svcStmt->fetchAll();

$optStmt = $db->query("SELECT * FROM options WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$options = $optStmt->fetchAll();

$optionsBySvc = [];
foreach ($options as $o) {
    $optionsBySvc[$o['service_id']][] = $o;
}

$servicesByCat = [];
foreach ($services as $s) {
    $s['options'] = $optionsBySvc[$s['id']] ?? [];
    $servicesByCat[$s['category_id']][] = $s;
}

$catData = [];
foreach ($categories as $c) {
    $catData[] = [
        'cat' => $c,
        'services' => $servicesByCat[$c['id']] ?? []
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <span class="page-header__eyebrow">Wellness &amp; Beauty · Build Your Session</span>
  <h1 class="page-header__title">Spa Menu &amp; Booking</h1>
  <p class="page-header__desc">
    Pick the services you want. We'll show you the running live total as you choose.
  </p>
</div>

<section class="section" style="padding-top:0;">
  <div class="container container--lg">

    <form method="POST" id="spaMenuForm" action="/spa_menu.php">
      <input type="hidden" name="booking_type" value="spa_menu" />

      <!-- ── Category chips ─────────────────────────────── -->
      <div class="cat-chips reveal" style="margin-bottom:32px;">
        <?php foreach ($catData as $i => $cd): ?>
          <a
            href="#cat-<?php echo $cd['cat']['id']; ?>"
            class="cat-chip <?php echo $i === 0 ? 'is-active' : ''; ?>"
            data-cat="<?php echo $cd['cat']['id']; ?>"
          >
            <span class="cat-chip__icon"><?php echo $cd['cat']['icon'] ?: '✨'; ?></span>
            <span class="cat-chip__name"><?php echo htmlspecialchars($cd['cat']['name']); ?></span>
            <span class="cat-chip__count"><?php echo count($cd['services']); ?></span>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- ── Category blocks ─────────────────────────────── -->
      <?php foreach ($catData as $cd):
          $c = $cd['cat'];
          $servicesList = $cd['services'];
      ?>
      <section class="cat-block reveal" id="cat-<?php echo $c['id']; ?>" data-cat="<?php echo $c['id']; ?>">

        <div class="cat-block__header">
          <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-size:24px;"><?php echo $c['icon'] ?: '✨'; ?></span>
            <div>
              <h3 class="cat-block__title"><?php echo htmlspecialchars($c['name']); ?></h3>
              <p class="cat-block__sub"><?php echo count($servicesList); ?> services available</p>
            </div>
          </div>
          <button type="button" class="cat-block__toggle" data-toggle="<?php echo $c['id']; ?>" aria-label="Toggle <?php echo htmlspecialchars($c['name']); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </div>

        <div class="menu-grid cat-block__body" id="cat-body-<?php echo $c['id']; ?>">
        <?php foreach ($servicesList as $s):
            $opts = $s['options'] ?? [];
            $firstPrice = !empty($opts[0]) ? (float)$opts[0]['price_ngn'] : 0.0;
            $multiOpt = count($opts) > 1;
        ?>
          <div class="menu-item">

            <label class="menu-item__check">
              <input
                type="checkbox"
                name="svc_selected[]"
                class="svc-select"
                value="<?php echo $s['id']; ?>"
                data-sid="<?php echo $s['id']; ?>"
              />
              <span class="menu-item__box"></span>
            </label>

            <div class="menu-item__main">
              <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
                <div>
                  <h4 class="menu-item__title"><?php echo htmlspecialchars($s['name']); ?></h4>
                  <?php if (!empty($s['description'])): ?>
                    <p class="menu-item__desc"><?php echo htmlspecialchars($s['description']); ?></p>
                  <?php endif; ?>
                </div>
                <div class="menu-item__price" data-svc-price="<?php echo $firstPrice; ?>">
                  <?php echo priceFmt($firstPrice); ?>
                </div>
              </div>

              <div class="menu-item__controls">

                <?php if ($multiOpt): ?>
                <label class="menu-item__opt">
                  <span class="menu-item__lbl">Option / Size</span>
                  <select name="svc_option[<?php echo $s['id']; ?>]" class="form-select svc-option" data-sid="<?php echo $s['id']; ?>">
                    <?php foreach ($opts as $o): ?>
                      <option value="<?php echo $o['id']; ?>" data-price="<?php echo $o['price_ngn']; ?>">
                        <?php echo htmlspecialchars($o['option_label']); ?>&nbsp; · <?php echo priceFmt($o['price_ngn']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <?php else: ?>
                  <label class="menu-item__opt" style="visibility:hidden;">
                    <span class="menu-item__lbl">Only option</span>
                    <select class="form-select svc-option" disabled></select>
                  </label>
                  <?php if (!empty($opts[0])): ?>
                    <input type="hidden" name="svc_option[<?php echo $s['id']; ?>]" value="<?php echo $opts[0]['id']; ?>" />
                  <?php endif; ?>
                <?php endif; ?>

                <div class="menu-item__qty">
                  <span class="menu-item__lbl">Qty</span>
                  <div class="qty-stepper">
                    <button type="button" class="qty-btn qty-btn--minus" data-sid="<?php echo $s['id']; ?>" aria-label="Decrease">−</button>
                    <input
                      type="number"
                      min="1" max="9"
                      value="1"
                      name="svc_qty[<?php echo $s['id']; ?>]"
                      class="qty-input svc-qty"
                      data-sid="<?php echo $s['id']; ?>"
                      aria-label="Quantity"
                    />
                    <button type="button" class="qty-btn qty-btn--plus" data-sid="<?php echo $s['id']; ?>" aria-label="Increase">+</button>
                  </div>
                </div>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
      </section>
      <?php endforeach; ?>

      <!-- ── Sticky summary bar ─────────────────────────── -->
      <div class="sticky-summary" id="stickySummary" hidden>
        <div class="container container--lg sticky-summary__inner">
          <div>
            <div class="sticky-summary__count"><span id="countEl">0</span> service<span id="countPlural">s</span> selected</div>
            <div class="sticky-summary__total">Grand Total: <span id="totalEl">₦0</span></div>
          </div>
          <div style="display:flex; gap:12px; align-items:center;">
            <button type="button" class="btn btn--ghost btn--sm" id="clearAllBtn">Clear all</button>
            <button type="submit" class="btn btn--primary" id="continueBtn">
              Review &amp; Continue
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<style>
  .cat-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .cat-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    background: var(--surface, #fff);
    border: 1px solid var(--border-soft, rgba(27,20,16,0.1));
    color: var(--text-soft, #555);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s ease;
  }
  .cat-chip:hover { border-color: var(--primary); color: var(--fg); }
  .cat-chip.is-active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    box-shadow: 0 4px 14px rgba(171, 122, 53, 0.3);
  }
  .cat-chip__icon { font-size: 16px; }
  .cat-chip__count {
    background: rgba(0,0,0,0.06);
    color: inherit;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 999px;
  }
  .cat-chip.is-active .cat-chip__count {
    background: rgba(255,255,255,0.25);
    color: #fff;
  }
  .cat-block {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(27,20,16,0.08);
    padding: 24px;
    margin-bottom: 28px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  }
  .cat-block__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(27,20,16,0.08);
  }
  .cat-block__title {
    font-family: var(--font-serif);
    font-size: 22px;
    color: var(--fg);
    margin: 0;
  }
  .cat-block__sub {
    font-size: 13px;
    color: var(--muted);
    margin: 2px 0 0;
  }
  .cat-block__toggle {
    background: none;
    border: 1px solid rgba(27,20,16,0.12);
    border-radius: 8px;
    padding: 6px;
    cursor: pointer;
    color: var(--muted);
    transition: transform .2s ease;
  }
  .cat-block.is-collapsed .cat-block__body { display: none; }
  .cat-block.is-collapsed .cat-block__toggle { transform: rotate(-90deg); }
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
  }
  .menu-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    border: 1px solid rgba(27,20,16,0.08);
    border-radius: 12px;
    padding: 16px;
    background: #faf7f2;
    transition: all .2s ease;
  }
  .menu-item:has(.svc-select:checked) {
    background: #fffdf9;
    border-color: var(--primary);
    box-shadow: 0 4px 14px rgba(171,122,53,0.12);
  }
  .menu-item__check {
    margin-top: 2px;
    cursor: pointer;
  }
  .menu-item__check input { display: none; }
  .menu-item__box {
    display: block;
    width: 20px;
    height: 20px;
    border-radius: 5px;
    border: 1.5px solid rgba(27,20,16,0.3);
    background: #fff;
    position: relative;
    transition: all .15s ease;
  }
  .menu-item__check input:checked + .menu-item__box {
    background: var(--primary);
    border-color: var(--primary);
  }
  .menu-item__check input:checked + .menu-item__box::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
  }
  .menu-item__main { flex: 1; min-width: 0; }
  .menu-item__title { font-size: 15px; font-weight: 600; color: var(--fg); margin: 0; }
  .menu-item__desc { font-size: 12px; color: var(--muted); margin: 4px 0 0; line-height: 1.4; }
  .menu-item__price { font-family: var(--font-serif); font-size: 16px; font-weight: 700; color: var(--primary); white-space: nowrap; }
  .menu-item__controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    padding-top: 10px;
    border-top: 1px dashed rgba(27,20,16,0.1);
  }
  .menu-item__lbl { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-bottom: 2px; }
  .qty-stepper { display: inline-flex; align-items: center; border: 1px solid rgba(27,20,16,0.15); border-radius: 6px; background: #fff; }
  .qty-btn { background: none; border: none; padding: 4px 8px; cursor: pointer; font-size: 14px; color: var(--fg); }
  .qty-input { width: 30px; text-align: center; border: none; font-size: 13px; font-weight: 600; background: transparent; }
  .sticky-summary {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(8px);
    border-top: 1px solid rgba(27,20,16,0.12);
    box-shadow: 0 -8px 24px rgba(0,0,0,0.06);
    z-index: 99;
    padding: 14px 0;
    transform: translateY(100%);
    transition: transform .25s ease;
  }
  .sticky-summary:not([hidden]) { transform: translateY(0); }
  .sticky-summary__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }
  .sticky-summary__count { font-size: 13px; color: var(--muted); }
  .sticky-summary__total { font-family: var(--font-serif); font-size: 20px; font-weight: 700; color: var(--primary); }
</style>

<script>
(function() {
  const $ = sel => document.querySelector(sel);
  const $$ = sel => Array.from(document.querySelectorAll(sel));

  const form     = $('#spaMenuForm');
  const sticky   = $('#stickySummary');
  const countEl  = $('#countEl');
  const pluralEl = $('#countPlural');
  const totalEl  = $('#totalEl');
  const clearBtn = $('#clearAllBtn');
  const contBtn  = $('#continueBtn');

  const fmtPrice = n => '₦' + Math.round(n).toLocaleString('en-NG');

  function recalc() {
    let total = 0;
    let count = 0;

    $$('.svc-select:checked').forEach(chk => {
      const sid = chk.dataset.sid;
      const optSel = $(`.svc-option[data-sid="${sid}"]`);
      let price = 0;
      if (optSel && optSel.options && optSel.options.length) {
        price = parseFloat(optSel.options[optSel.selectedIndex].dataset.price || '0');
      } else {
        const item = chk.closest('.menu-item');
        const priceEl = item.querySelector('.menu-item__price');
        price = parseFloat(priceEl?.dataset.svcPrice || '0');
      }

      const qtyInput = $(`.svc-qty[data-sid="${sid}"]`);
      const qty = parseInt(qtyInput ? qtyInput.value : '1', 10) || 1;

      total += price * qty;
      count += 1;
    });

    countEl.textContent = count;
    pluralEl.textContent = count === 1 ? '' : 's';
    totalEl.textContent = fmtPrice(total);

    if (count > 0) {
      sticky.removeAttribute('hidden');
    } else {
      sticky.setAttribute('hidden', '');
    }

    return { count, total };
  }

  function refreshPrices() {
    $$('.svc-option').forEach(sel => {
      const sid = sel.dataset.sid;
      const opt = sel.options[sel.selectedIndex];
      if (opt && opt.dataset.price) {
        const priceEl = sel.closest('.menu-item')?.querySelector('.menu-item__price');
        if (priceEl) {
          priceEl.textContent = fmtPrice(parseFloat(opt.dataset.price));
          priceEl.dataset.svcPrice = opt.dataset.price;
        }
      }
    });
  }

  form.addEventListener('change', e => {
    if (e.target.matches('.svc-select') || e.target.matches('.svc-option')) {
      refreshPrices();
      recalc();
    }
  });

  form.addEventListener('input', e => {
    if (e.target.matches('.svc-qty')) {
      let v = parseInt(e.target.value || '1', 10);
      v = Math.max(1, Math.min(9, isNaN(v) ? 1 : v));
      e.target.value = v;
      recalc();
    }
  });

  form.addEventListener('click', e => {
    const minus = e.target.closest('.qty-btn--minus');
    const plus  = e.target.closest('.qty-btn--plus');
    const sid = (minus || plus)?.dataset.sid;
    if (sid) {
      const input = $(`.svc-qty[data-sid="${sid}"]`);
      let v = parseInt(input.value || '1', 10);
      v = Math.max(1, Math.min(9, v + (minus ? -1 : 1)));
      input.value = v;
      recalc();
      e.preventDefault();
    }

    const toggle = e.target.closest('[data-toggle]');
    if (toggle) {
      const cid = toggle.dataset.toggle;
      toggle.classList.toggle('is-collapsed');
      $(`#cat-${cid}`).classList.toggle('is-collapsed');
      e.preventDefault();
    }

    if (e.target.closest('.menu-item__main')) {
      const item = e.target.closest('.menu-item');
      if (item && !e.target.closest('.svc-option') && !e.target.closest('.qty-btn') && !e.target.closest('.qty-input')) {
        const chk = item.querySelector('.svc-select');
        if (chk) { chk.checked = !chk.checked; refreshPrices(); recalc(); }
      }
    }
  });

  // Category chips smooth navigation
  const chips = $$('.cat-chip');
  const blocks = $$('.cat-block');
  const setActive = id => {
    chips.forEach(c => c.classList.toggle('is-active', c.dataset.cat === id));
  };
  const io = new IntersectionObserver(entries => {
    entries.forEach(en => {
      if (en.isIntersecting) setActive(en.target.dataset.cat);
    });
  }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });
  blocks.forEach(b => io.observe(b));

  chips.forEach(c => {
    c.addEventListener('click', e => {
      e.preventDefault();
      const id = c.dataset.cat;
      setActive(id);
      const t = document.getElementById(`cat-${id}`);
      if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  clearBtn.addEventListener('click', () => {
    $$('.svc-select:checked').forEach(c => c.checked = false);
    $$('.svc-qty').forEach(q => q.value = '1');
    refreshPrices();
    recalc();
  });

  contBtn.addEventListener('click', e => {
    const { count } = recalc();
    if (count === 0) {
      e.preventDefault();
      alert('Please select at least one service to continue.');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });

  refreshPrices();
  recalc();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
