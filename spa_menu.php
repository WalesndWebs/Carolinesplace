<?php
require_once __DIR__ . '/api/db.php';

$isReviewStep = ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['booking_type'] ?? '') === 'spa_menu');

/* ════════════════════════════════════════════════════════════════
   SPA FLOW · STEP 2 of 2 — Review selected items + customer form
   ══════════════════════════════════════════════════════════════ */
if ($isReviewStep) {

    $pageTitle = "Review Your Spa Booking";
    $pageDesc  = "Review your selected spa services and complete your booking at Caroline's Place.";
    $root      = './';
    $current   = 'book';

    $selected = $_POST['svc_selected'] ?? [];
    $svcOpt   = $_POST['svc_option']   ?? [];
    $svcQty   = $_POST['svc_qty']      ?? [];

    $lines = [];
    $total = 0;

    if (is_array($selected) && $selected) {
        $db = getDB();

        foreach ($selected as $sidRaw) {
            $sid = (int)$sidRaw;
            if ($sid <= 0) continue;

            $optId = isset($svcOpt[$sid]) ? (int)$svcOpt[$sid] : 0;
            $qty   = isset($svcQty[$sid])  ? max(1, min(9, (int)$svcQty[$sid])) : 1;
            if ($optId <= 0) continue;

            $stmt = $db->prepare("
                SELECT s.name AS service_name,
                       o.option_label,
                       o.price_ngn AS unit_price
                FROM spa_services s
                JOIN spa_service_options o ON o.service_id = s.id
                WHERE s.id = ? AND o.id = ?
                  AND s.is_active = 1 AND o.is_active = 1
                LIMIT 1
            ");
            $stmt->execute([$sid, $optId]);
            $row = $stmt->fetch();

            if (!$row) continue;

            $unitPrice = (float)$row['unit_price'];
            $lineTotal = $unitPrice * $qty;

            $lines[] = [
                'sid'          => $sid,
                'option_id'    => $optId,
                'service_name' => $row['service_name'],
                'option_label' => $row['option_label'],
                'unit_price'   => $unitPrice,
                'qty'          => $qty,
                'line_total'   => $lineTotal,
            ];
            $total += $lineTotal;
        }
    }

    if (!$lines) {
        header('Location: spa_menu.php');
        exit;
    }

    $timeSlots = [
      '10:30 AM','11:00 AM','11:30 AM',
      '12:00 PM','12:30 PM','01:00 PM','01:30 PM','02:00 PM','02:30 PM',
      '03:00 PM','03:30 PM','04:00 PM','04:30 PM','05:00 PM','05:30 PM',
      '06:00 PM',
    ];

    include __DIR__ . '/includes/header.php';
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

    <div class="booking-card" style="max-width:900px;">

      <div style="margin-bottom:40px;">
        <div class="form-section-title">Selected Services</div>

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
                <tr style="border-bottom:1px solid var(--border-soft);">
                  <td style="padding:14px 10px;font-weight:500;"><?= htmlspecialchars($l['service_name']) ?></td>
                  <td style="padding:14px 10px;color:var(--muted);"><?= htmlspecialchars($l['option_label']) ?></td>
                  <td style="padding:14px 10px;text-align:right;font-variant-numeric:tabular-nums;"><?= priceFmt($l['unit_price']) ?></td>
                  <td style="padding:14px 10px;text-align:center;font-variant-numeric:tabular-nums;"><?= (int)$l['qty'] ?></td>
                  <td style="padding:14px 10px;text-align:right;font-weight:600;font-variant-numeric:tabular-nums;"><?= priceFmt($l['line_total']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" style="padding:20px 10px 10px;text-align:right;font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);">Grand Total</td>
                <td style="padding:20px 10px 10px;text-align:right;font-family:var(--font-serif);font-size:2.2rem;font-weight:700;color:var(--primary);font-variant-numeric:tabular-nums;"><?= priceFmt($total) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <form method="POST" action="api/book_spa.php" novalidate>

        <?php foreach ($lines as $l): ?>
          <input type="hidden" name="line_service_id[]" value="<?= (int)$l['sid'] ?>" />
          <input type="hidden" name="line_option_id[]"  value="<?= (int)$l['option_id'] ?>" />
          <input type="hidden" name="line_unit[]"       value="<?= htmlspecialchars($l['unit_price']) ?>" />
          <input type="hidden" name="line_qty[]"        value="<?= (int)$l['qty'] ?>" />
        <?php endforeach; ?>
        <input type="hidden" name="total_amount" value="<?= htmlspecialchars($total) ?>" />

        <div style="margin-bottom:40px;">
          <div class="form-section-title">Guest Details</div>

          <div class="form-group">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-input" placeholder="John Doe" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required />
            </div>
            <div class="form-group">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" id="phone" name="phone" class="form-input" placeholder="+234&hellip;" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="preferred_date" class="form-label">Preferred Date</label>
              <input type="date" id="preferred_date" name="preferred_date" class="form-input" required />
            </div>
            <div class="form-group">
              <label for="preferred_time" class="form-label">Preferred Time</label>
              <select id="preferred_time" name="preferred_time" class="form-select" required>
                <option value="">Select a time</option>
                <?php foreach ($timeSlots as $t): ?>
                  <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="notes" class="form-label">Special Requests / Notes</label>
            <textarea id="notes" name="notes" class="form-textarea" placeholder="Any specific preferences or requirements&hellip;"></textarea>
          </div>
        </div>

        <div class="form-spacer">
          <button type="submit" class="btn btn--primary btn--full btn--lg">
            Confirm &amp; Submit Booking
          </button>
          <p class="form-note">
            Our concierge team will contact you shortly to confirm your reservation.
          </p>
        </div>

      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
/* ════════════════════════════════════════════════════════════════
   SPA FLOW · STEP 1 of 2 — Full service MENU · category chips,
   service cards, option dropdowns, qty steppers, live total
   ══════════════════════════════════════════════════════════════ */
} else {

    $pageTitle = "Spa Menu & Booking";
    $pageDesc  = "Select hair, nail, massage, facial and body services. Live total as you build your perfect session.";
    $root      = './';
    $current   = 'book';
    $db        = getDB();

    // ── Fallback category icons (always works, even if icon column missing in DB!) ──
    $catIcons = [
        'Spa Section'       => '🧖‍♀️',
        'Massage'           => '💆',
        'Waxing'            => '✨',
        'Body Treatment'    => '🧴',
        'Hair Section'      => '💇',
        'Nails Price List'  => '💅',
        'Pedicure Section'  => '🦶',
        // Legacy fallback (backwards compat for any test runs before reseed):
        'Massage Therapy'         => '💆',
        'Nail Care · Manicure'    => '💅',
        'Nail Care · Pedicure'    => '🦶',
        'Facials & Skin Care'     => '🧖‍♀️',
        'Body Treatments'         => '🧴',
        'Hair Styling'            => '💇',
        'Hair Removal & Waxing'   => '✨',
        'Makeup'                  => '💄',
        'Couples & Packages'      => '💑',
        'Kids Spa & Extras'       => '🎀',
    ];

    $categories = $db->query("
        SELECT c.id, c.name, c.description, c.sort_order,
               (SELECT COUNT(*) FROM spa_services s WHERE s.category_id = c.id AND s.is_active = 1) AS svc_count
        FROM spa_categories c
        WHERE c.is_active = 1
        ORDER BY c.sort_order, c.id
    ")->fetchAll();

    $catData = [];
    foreach ($categories as $c) {
        $services = $db->prepare("
            SELECT s.id, s.name, s.description, s.sort_order
            FROM spa_services s
            WHERE s.category_id = ? AND s.is_active = 1
            ORDER BY s.sort_order, s.id
        ");
        $services->execute([$c['id']]);
        $svcRows = $services->fetchAll();
        foreach ($svcRows as &$s) {
            $opts = $db->prepare("
                SELECT id, option_label, price_ngn
                FROM spa_service_options
                WHERE service_id = ? AND is_active = 1
                ORDER BY sort_order, id
            ");
            $opts->execute([$s['id']]);
            $s['options'] = $opts->fetchAll();
        }
        unset($s);
        $catData[] = ['cat' => $c, 'services' => $svcRows];
    }

    include __DIR__ . '/includes/header.php';
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

    <form method="POST" id="spaMenuForm" action="spa_menu.php">
      <input type="hidden" name="booking_type" value="spa_menu" />

      <!-- ── Category chips ─────────────────────────────── -->
      <div class="cat-chips reveal" style="margin-bottom:32px;">
        <?php foreach ($catData as $i => $cd):
            $cid = $cd['cat']['id']; ?>
          <a
            href="#cat-<?= (int)$cid ?>"
            class="cat-chip <?= $i === 0 ? 'is-active' : '' ?>"
            data-cat="<?= (int)$cid ?>"
          >
            <span class="cat-chip__icon"><?= htmlspecialchars($catIcons[$cd['cat']['name']] ?? (isset($cd['cat']['icon']) ? $cd['cat']['icon'] : null) ?? '✨') ?></span>
            <span class="cat-chip__name"><?= htmlspecialchars($cd['cat']['name']) ?></span>
            <span class="cat-chip__count"><?= (int)$cd['cat']['svc_count'] ?></span>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- ── Category blocks ─────────────────────────────── -->
      <?php foreach ($catData as $i => $cd):
          $c = $cd['cat'];
          $services = $cd['services'];
      ?>
      <section class="cat-block reveal" id="cat-<?= (int)$c['id'] ?>" data-cat="<?= (int)$c['id'] ?>">

        <div class="cat-block__header">
          <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-size:24px;"><?= htmlspecialchars($catIcons[$c['name']] ?? (isset($c['icon']) ? $c['icon'] : null) ?? '✨') ?></span>
            <div>
              <h3 class="cat-block__title"><?= htmlspecialchars($c['name']) ?></h3>
              <p class="cat-block__sub"><?= (int)$c['svc_count'] ?> services available</p>
            </div>
          </div>
          <button type="button" class="cat-block__toggle" data-toggle="<?= (int)$c['id'] ?>" aria-label="Toggle <?= htmlspecialchars($c['name']) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </div>

        <div class="menu-grid cat-block__body" id="cat-body-<?= (int)$c['id'] ?>">
        <?php foreach ($services as $s):
            $opts = $s['options'];
            $firstPrice = (float)($opts[0]['price_ngn'] ?? 0);
            $multiOpt = count($opts) > 1;
        ?>
          <div class="menu-item">

            <label class="menu-item__check">
              <input
                type="checkbox"
                name="svc_selected[]"
                class="svc-select"
                value="<?= (int)$s['id'] ?>"
                data-sid="<?= (int)$s['id'] ?>"
              />
              <span class="menu-item__box"></span>
            </label>

            <div class="menu-item__main">
              <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
                <div>
                  <h4 class="menu-item__title"><?= htmlspecialchars($s['name']) ?></h4>
                  <?php if (!empty($s['description'])): ?>
                    <p class="menu-item__desc"><?= htmlspecialchars($s['description']) ?></p>
                  <?php endif; ?>
                </div>
                <div class="menu-item__price" data-svc-price="<?= (int)$firstPrice ?>">
                  <?= priceFmt($firstPrice) ?>
                </div>
              </div>

              <div class="menu-item__controls">

                <?php if ($multiOpt): ?>
                <label class="menu-item__opt">
                  <span class="menu-item__lbl">Option / Size</span>
                  <select name="svc_option[<?= (int)$s['id'] ?>]" class="form-select svc-option" data-sid="<?= (int)$s['id'] ?>">
                    <?php foreach ($opts as $o): ?>
                      <option value="<?= (int)$o['id'] ?>" data-price="<?= (float)$o['price_ngn'] ?>">
                        <?= htmlspecialchars($o['option_label']) ?>&nbsp; · <?= priceFmt((float)$o['price_ngn']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <?php else: ?>
                  <label class="menu-item__opt" style="visibility:hidden;">
                    <span class="menu-item__lbl">Only option</span>
                    <select class="form-select svc-option" disabled></select>
                  </label>
                  <?php if (!empty($opts)): ?>
                    <input type="hidden" name="svc_option[<?= (int)$s['id'] ?>]" value="<?= (int)$opts[0]['id'] ?>" />
                  <?php endif; ?>
                <?php endif; ?>

                <div class="menu-item__qty">
                  <span class="menu-item__lbl">Qty</span>
                  <div class="qty-stepper">
                    <button type="button" class="qty-btn qty-btn--minus" data-sid="<?= (int)$s['id'] ?>" aria-label="Decrease">−</button>
                    <input
                      type="number"
                      min="1" max="9"
                      value="1"
                      name="svc_qty[<?= (int)$s['id'] ?>]"
                      class="qty-input svc-qty"
                      data-sid="<?= (int)$s['id'] ?>"
                      aria-label="Quantity"
                    />
                    <button type="button" class="qty-btn qty-btn--plus" data-sid="<?= (int)$s['id'] ?>" aria-label="Increase">+</button>
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
    background: var(--surface);
    border: 1px solid var(--border-soft);
    color: var(--text-soft);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s ease;
  }
  .cat-chip:hover { border-color: var(--primary); color: var(--text); }
  .cat-chip.is-active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    box-shadow: 0 4px 14px rgba(171, 122, 53, 0.3);
  }
  .cat-chip__icon { font-size: 16px; }
  .cat-chip__count {
    background: rgba(255,255,255,0.18);
    color: inherit;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
  }
  .cat-chip:not(.is-active) .cat-chip__count {
    background: #f2ebe0;
    color: var(--primary);
  }
  .cat-block {
    margin-bottom: 40px;
    background: var(--surface);
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 26px 28px 30px;
    overflow: hidden;
  }
  .cat-block__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--border-soft);
  }
  .cat-block__title {
    margin: 0;
    font-family: var(--font-serif);
    font-size: 22px;
    color: var(--text);
    letter-spacing: -0.3px;
  }
  .cat-block__sub {
    margin: 2px 0 0;
    font-size: 13px;
    color: var(--muted);
    letter-spacing: 0.04em;
  }
  .cat-block__toggle {
    background: transparent;
    border: 1px solid var(--border-soft);
    color: var(--text-soft);
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s ease;
  }
  .cat-block__toggle:hover {
    color: var(--primary);
    border-color: var(--primary);
  }
  .cat-block__toggle.is-collapsed svg {
    transform: rotate(180deg);
  }
  .cat-block__toggle svg { transition: transform .25s ease; }
  .cat-block.is-collapsed .cat-block__body {
    display: none;
  }
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }
  @media (max-width: 900px) {
    .menu-grid { grid-template-columns: 1fr; }
    .cat-block { padding: 20px 18px 24px; }
  }
  .menu-item {
    display: flex;
    gap: 14px;
    padding: 18px 16px;
    border-radius: 14px;
    border: 1px solid var(--border-soft);
    background: #fff;
    transition: all .2s ease;
    cursor: pointer;
  }
  .menu-item:hover {
    border-color: #e2cfa9;
    box-shadow: 0 4px 18px rgba(17,17,17,0.04);
  }
  .menu-item:has(.svc-select:checked) {
    border-color: var(--primary);
    background: linear-gradient(180deg, #fffaf0 0%, #fff 100%);
    box-shadow: 0 3px 14px rgba(171,122,53,0.1);
  }
  .menu-item__check {
    padding-top: 2px;
    cursor: pointer;
    flex-shrink: 0;
  }
  .menu-item__check input { position: absolute; opacity: 0; pointer-events: none; }
  .menu-item__box {
    display: block;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 2px solid var(--border);
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
    left: 5px;
    top: 1px;
    width: 7px;
    height: 12px;
    border: 2px solid #fff;
    border-top: 0;
    border-left: 0;
    transform: rotate(45deg);
  }
  .menu-item__main { flex: 1; min-width: 0; }
  .menu-item__title {
    margin: 0 0 4px;
    font-family: var(--font-serif);
    font-size: 16px;
    color: var(--text);
    line-height: 1.35;
  }
  .menu-item__desc {
    margin: 0 0 12px;
    font-size: 13px;
    color: var(--text-soft);
    line-height: 1.5;
  }
  .menu-item__price {
    font-family: var(--font-serif);
    font-weight: 700;
    color: var(--primary);
    font-size: 18px;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .menu-item__controls {
    display: grid;
    grid-template-columns: 1fr 130px;
    gap: 12px;
    margin-top: 14px;
    align-items: end;
  }
  @media (max-width: 560px) {
    .menu-item__controls { grid-template-columns: 1fr; }
  }
  .menu-item__opt { display: block; min-width: 0; }
  .menu-item__lbl {
    display: block;
    margin-bottom: 6px;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--muted);
    font-weight: 600;
  }
  .menu-item__opt .form-select { padding: 8px 10px; font-size: 13px; }
  .menu-item__qty { display: block; }
  .qty-stepper {
    display: inline-flex;
    align-items: stretch;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
  }
  .qty-btn {
    width: 34px;
    height: 34px;
    border: 0;
    background: #f7f2e8;
    color: var(--primary);
    font-weight: 700;
    cursor: pointer;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease;
  }
  .qty-btn:hover { background: #eee1c8; }
  .qty-input {
    width: 52px;
    border: 0;
    border-radius: 0;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    padding: 0 6px;
    outline: none;
    color: var(--text);
  }
  .qty-input::-webkit-outer-spin-button,
  .qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  .qty-input[type=number] { -moz-appearance: textfield; }
  .sticky-summary {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 90;
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid var(--border-soft);
    box-shadow: 0 -6px 28px rgba(17,17,17,0.08);
    animation: slideUp .25s ease;
  }
  @keyframes slideUp { from { transform: translateY(100%); opacity:0; } to { transform:none; opacity:1; } }
  .sticky-summary__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    gap: 18px;
    min-height: 76px;
  }
  .sticky-summary__count {
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--muted);
    font-weight: 600;
    margin-bottom: 3px;
  }
  .sticky-summary__count span { color: var(--primary); }
  .sticky-summary__total {
    font-family: var(--font-serif);
    font-size: 22px;
    font-weight: 700;
    color: var(--text);
  }
  .sticky-summary__total span {
    color: var(--primary);
    font-variant-numeric: tabular-nums;
  }
  @media (max-width: 640px) {
    .sticky-summary__inner { flex-direction: column; align-items: stretch; padding: 12px 18px 14px; }
    .sticky-summary__total { font-size: 18px; }
    .sticky-summary__inner > div:last-child { justify-content: space-between; display:flex; }
  }
  body { padding-bottom: 120px; }
</style>

<script>
(() => {
  const $ = (sel, el=document) => el.querySelector(sel);
  const $$ = (sel, el=document) => [...el.querySelectorAll(sel)];
  const fmt = n => '₦' + new Intl.NumberFormat('en-NG').format(Math.round(n));

  const summary = $('#stickySummary');
  const countEl = $('#countEl');
  const totalEl = $('#totalEl');
  const countPlural = $('#countPlural');
  const clearBtn = $('#clearAllBtn');
  const form = $('#spaMenuForm');
  const contBtn = $('#continueBtn');

  function getOptionPrice(sid) {
    const sel = $(`.svc-option[data-sid="${sid}"]`);
    if (sel && sel.value) {
      const o = sel.options[sel.selectedIndex];
      return parseFloat(o?.dataset?.price || '0');
    }
    const hidden = $(`input[name="svc_option[${sid}]"]`);
    if (hidden) {
      const parent = $(`.menu-item:has(input[name="svc_selected[]"][value="${sid}"])`);
      const priceEl = parent?.querySelector('[data-svc-price]');
      return parseFloat(priceEl?.dataset?.svcPrice || '0');
    }
    return 0;
  }

  function refreshPrices() {
    $$('.svc-select').forEach(chk => {
      const sid = chk.dataset.sid;
      const unit  = getOptionPrice(sid);
      const disp = $(`[data-svc-price]`, chk.closest('.menu-item'));
      if (disp) {
        disp.textContent = fmt(unit);
        disp.dataset.svcPrice = unit;
      }
    });
  }

  function recalc() {
    const selected = $$('.svc-select:checked');
    let count = 0, total = 0;
    selected.forEach(chk => {
      const sid = chk.dataset.sid;
      const unit  = getOptionPrice(sid);
      const qtyIn = $(`.svc-qty[data-sid="${sid}"]`);
      const qty   = Math.max(1, Math.min(9, parseInt(qtyIn?.value || '1', 10)));
      count += qty;
      total += unit * qty;
    });
    countEl.textContent = count.toString();
    totalEl.textContent = fmt(total);
    countPlural.textContent = (count === 1 ? '' : 's');
    summary.hidden = count === 0;
    return { count, total };
  }

  // ── Event bindings ───────────────────────────────────
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

  // Category chip active on scroll
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

  // Clear all
  clearBtn.addEventListener('click', () => {
    $$('.svc-select:checked').forEach(c => c.checked = false);
    $$('.svc-qty').forEach(q => q.value = '1');
    refreshPrices();
    recalc();
  });

  // Submit guard
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

<?php include __DIR__ . '/includes/footer.php'; ?>
<?php } ?>
