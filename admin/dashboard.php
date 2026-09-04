<?php
session_start();

// Auth guard
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/db.php';
$db = getDB();

// ── Filters from query string ────────────────────────────────
$divisionFilter = in_array($_GET['division'] ?? '', ['clubhouse', 'spa']) ? $_GET['division'] : 'all';
$statusFilter   = in_array($_GET['status'] ?? '', ['pending','confirmed','completed','cancelled']) ? $_GET['status'] : 'all';

// ── SPA filters ──────────────────────────────────────────────
$spaStatusFilter = in_array($_GET['spa_status'] ?? '', ['pending','confirmed','completed','cancelled']) ? $_GET['spa_status'] : 'all';

// ── Stats ────────────────────────────────────────────────────
// ⚠️ SATURDAY 29TH SPA-ONLY SCHEMA: `bookings` table does NOT exist
//    (that was OLD Clubhouse division). Saturday only has `spa_bookings` table.
//    Wrap in try/catch so SPA-only deployments don't BLANK SCREEN CRASH.
$stats = [
    'total'     => 0,
    'pending'   => 0,
    'confirmed' => 0,
    'completed' => 0,
];
try {
    $_hasBookings = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='bookings'")->fetchColumn();
    if ($_hasBookings || ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' && $db->query("SHOW TABLES LIKE 'bookings'")->fetchColumn())) {
        $stats = $db->query("
          SELECT
            COUNT(*) AS total,
            SUM(status='pending')   AS pending,
            SUM(status='confirmed') AS confirmed,
            SUM(status='completed') AS completed
          FROM bookings
        ")->fetch();
    }
} catch (Throwable $e) { /* Fall through to zero stats — table doesn't exist in SPA-only build */ }

// ── SPA Stats ────────────────────────────────────────────────
$spaStats = $db->query("
  SELECT
    COUNT(*) AS total,
    SUM(status='pending')   AS pending,
    SUM(status='confirmed') AS confirmed,
    SUM(status='completed') AS completed,
    COALESCE(SUM(total_amount_ngn), 0) AS total_revenue
  FROM spa_bookings
")->fetch();

// ── Bookings query with filters ──────────────────────────────
$where  = [];
$params = [];
if ($divisionFilter !== 'all') { $where[] = "b.division = ?"; $params[] = $divisionFilter; }
if ($statusFilter   !== 'all') { $where[] = "b.status = ?";   $params[] = $statusFilter;   }

$bookings = [];
try {
    $_hasBookings2 = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='bookings'")->fetchColumn();
    $_hasServices  = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='services'")->fetchColumn();
    if (($_hasBookings2 && $_hasServices) || ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql')) {
        $sql  = "SELECT b.*, s.name AS service_label FROM bookings b LEFT JOIN services s ON b.service_id = s.id";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY b.created_at DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();
    }
} catch (Throwable $e) { /* Table bookings/services doesn't exist in SPA-only schema — leave empty */ }

// ── SPA Bookings query with filter ───────────────────────────
$spaWhere  = [];
$spaParams = [];
if ($spaStatusFilter !== 'all') { $spaWhere[] = "sb.status = ?"; $spaParams[] = $spaStatusFilter; }

$spaSql  = "SELECT sb.*, (SELECT COUNT(*) FROM spa_booking_items sbi WHERE sbi.booking_id = sb.id) AS item_count
            FROM spa_bookings sb";
if ($spaWhere) $spaSql .= " WHERE " . implode(' AND ', $spaWhere);
$spaSql .= " ORDER BY sb.created_at DESC LIMIT 200";

$spaStmt = $db->prepare($spaSql);
$spaStmt->execute($spaParams);
$spaBookings = $spaStmt->fetchAll();

// ── Status colour helper ─────────────────────────────────────
function statusClass(string $s): string {
    return match($s) {
        'pending'   => 'status--pending',
        'confirmed' => 'status--confirmed',
        'completed' => 'status--completed',
        'cancelled' => 'status--cancelled',
        default     => '',
    };
}

$displayName = htmlspecialchars($_SESSION['admin_display_name'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — Caroline's Place</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body style="background:var(--bg);">

<!-- ── Admin Nav ─────────────────────────────────────────────── -->
<nav class="admin-nav">
  <div class="admin-nav__brand">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--primary)"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    Admin
  </div>
  <a class="admin-nav__link" href="spa_products.php" style="margin-right:auto;color:var(--text-soft);text-decoration:none;font-weight:600;font-size:14px;">🛠️ Spa Products &amp; Pricing</a>
  <div class="admin-nav__right">
    <span class="admin-nav__user">Logged in as <?= $displayName ?></span>
    <button id="logoutBtn" class="admin-nav__logout">Logout</button>
  </div>
</nav>

<main class="admin-main">

  <!-- ── Stats ────────────────────────────────────────────────── -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card__label">Total Bookings</div>
      <div class="stat-card__value" id="statTotal"><?= (int)$stats['total'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__label">Pending</div>
      <div class="stat-card__value" id="statPending"><?= (int)$stats['pending'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__label">Confirmed</div>
      <div class="stat-card__value" id="statConfirmed"><?= (int)$stats['confirmed'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__label">Completed</div>
      <div class="stat-card__value" id="statCompleted"><?= (int)$stats['completed'] ?></div>
    </div>
  </div>

  <!-- ── Table ────────────────────────────────────────────────── -->
  <div class="table-card">
    <div class="table-card__header">
      <h2 class="table-card__title">Recent Bookings</h2>

      <div class="table-filters">
        <select id="divisionFilter" class="filter-select">
          <option value="all"      <?= $divisionFilter === 'all'       ? 'selected' : '' ?>>All Divisions</option>
          <option value="clubhouse"<?= $divisionFilter === 'clubhouse' ? 'selected' : '' ?>>The Club House</option>
          <option value="spa"      <?= $divisionFilter === 'spa'       ? 'selected' : '' ?>>N Lounge &amp; Spa</option>
        </select>
        <select id="statusFilter" class="filter-select">
          <option value="all"      <?= $statusFilter === 'all'       ? 'selected' : '' ?>>All Statuses</option>
          <option value="pending"  <?= $statusFilter === 'pending'   ? 'selected' : '' ?>>Pending</option>
          <option value="confirmed"<?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
          <option value="completed"<?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
          <option value="cancelled"<?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button id="refreshBtn" class="table-refresh" title="Refresh">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
        </button>
      </div>
    </div>

    <div class="overflow-x">
      <?php if (empty($bookings)): ?>
        <div class="no-bookings">No bookings found for the selected filters.</div>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Guest</th>
              <th>Division</th>
              <th>Service</th>
              <th>Date &amp; Time</th>
              <th>Status</th>
              <th>Payment</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <?php
                $bJson = rawurlencode(json_encode([
                  'id'             => $b['id'],
                  'reference_code' => $b['reference_code'],
                  'full_name'      => $b['full_name'],
                  'email'          => $b['email'],
                  'phone'          => $b['phone'],
                  'division'       => $b['division'],
                  'service_name'   => $b['service_name'],
                  'preferred_date' => $b['preferred_date'],
                  'preferred_time' => $b['preferred_time'],
                  'notes'          => $b['notes'],
                  'admin_notes'    => $b['admin_notes'],
                ]));
                $dateStr = date('M j, Y', strtotime($b['preferred_date']));
              ?>
              <tr>
                <td><span class="ref-code"><?= htmlspecialchars($b['reference_code']) ?></span></td>
                <td>
                  <div class="guest-name"><?= htmlspecialchars($b['full_name']) ?></div>
                  <div class="guest-sub"><?= htmlspecialchars($b['phone']) ?></div>
                </td>
                <td style="text-transform:capitalize;"><?= htmlspecialchars($b['division']) ?></td>
                <td><?= htmlspecialchars($b['service_name']) ?></td>
                <td>
                  <div><?= htmlspecialchars($dateStr) ?></div>
                  <div class="guest-sub"><?= htmlspecialchars($b['preferred_time']) ?></div>
                </td>
                <td>
                  <select
                    class="status-select <?= statusClass($b['status']) ?>"
                    data-id="<?= $b['id'] ?>"
                  >
                    <option value="pending"   <?= $b['status']==='pending'   ?'selected':'' ?>>Pending</option>
                    <option value="confirmed" <?= $b['status']==='confirmed' ?'selected':'' ?>>Confirmed</option>
                    <option value="completed" <?= $b['status']==='completed' ?'selected':'' ?>>Completed</option>
                    <option value="cancelled" <?= $b['status']==='cancelled' ?'selected':'' ?>>Cancelled</option>
                  </select>
                </td>
                <td>
                  <select
                    class="payment-select payment--<?= htmlspecialchars($b['payment_status']) ?>"
                    data-id="<?= $b['id'] ?>"
                  >
                    <option value="unpaid" <?= $b['payment_status']==='unpaid' ?'selected':'' ?>>Unpaid</option>
                    <option value="paid"   <?= $b['payment_status']==='paid'   ?'selected':'' ?>>Paid</option>
                  </select>
                </td>
                <td style="text-align:right;">
                  <button class="action-btn" onclick="openDetail('<?= $bJson ?>')">Details</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── Spa Bookings Section ──────────────────────────────────── -->
  <div style="margin-top:40px;">
    <div class="stats-grid" style="margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-card__label">Total Spa Bookings</div>
        <div class="stat-card__value"><?= (int)$spaStats['total'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Pending</div>
        <div class="stat-card__value" style="color:#92640a;"><?= (int)$spaStats['pending'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Confirmed</div>
        <div class="stat-card__value" style="color:#166534;"><?= (int)$spaStats['confirmed'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Total Revenue</div>
        <div class="stat-card__value" style="font-size:1.8rem;color:var(--primary);"><?= priceFmt((float)$spaStats['total_revenue']) ?></div>
      </div>
    </div>

    <div class="table-card">
      <div class="table-card__header">
        <h2 class="table-card__title">Spa Bookings (Multi-Service)</h2>

        <div class="table-filters">
          <select id="spaStatusFilter" class="filter-select" onchange="window.location.href='?spa_status='+this.value">
            <option value="all"      <?= $spaStatusFilter === 'all'       ? 'selected' : '' ?>>All Statuses</option>
            <option value="pending"  <?= $spaStatusFilter === 'pending'   ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed"<?= $spaStatusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="completed"<?= $spaStatusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled"<?= $spaStatusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
      </div>

      <div class="overflow-x">
        <?php if (empty($spaBookings)): ?>
          <div class="no-bookings">No spa bookings found for the selected filters.</div>
        <?php else: ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Ref</th>
                <th>Customer</th>
                <th>Date &amp; Time</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($spaBookings as $sb): ?>
                <?php
                  $dateStr = date('M j, Y', strtotime($sb['preferred_date']));
                  $sbJson  = rawurlencode(json_encode($sb, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP));
                ?>
                <tr>
                  <td><span class="ref-code"><?= htmlspecialchars($sb['reference_code']) ?></span></td>
                  <td>
                    <div class="guest-name"><?= htmlspecialchars($sb['full_name']) ?></div>
                    <div class="guest-sub"><?= htmlspecialchars($sb['phone']) ?></div>
                  </td>
                  <td>
                    <div><?= htmlspecialchars($dateStr) ?></div>
                    <div class="guest-sub"><?= htmlspecialchars($sb['preferred_time']) ?></div>
                  </td>
                  <td style="font-weight:500;"><?= (int)$sb['item_count'] ?> service(s)</td>                  <td style="font-weight:700;color:var(--primary);"><?= priceFmt((float)$sb['total_amount_ngn']) ?></td>
                  <td>
                    <select
                      class="status-select <?= statusClass($sb['status']) ?>"
                      data-id="<?= $sb['id'] ?>"
                      data-type="spa"
                    >
                      <option value="pending"   <?= $sb['status']==='pending'   ?'selected':'' ?>>Pending</option>
                      <option value="confirmed" <?= $sb['status']==='confirmed' ?'selected':'' ?>>Confirmed</option>
                      <option value="completed" <?= $sb['status']==='completed' ?'selected':'' ?>>Completed</option>
                      <option value="cancelled" <?= $sb['status']==='cancelled' ?'selected':'' ?>>Cancelled</option>
                    </select>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    <button
                      class="action-btn spa-view-items-btn"
                      data-id="<?= $sb['id'] ?>"
                      data-json="<?= $sbJson ?>"
                      style="margin-right:6px;">
                      🛒 View Services
                    </button>
                    <select
                      class="payment-select payment--<?= htmlspecialchars($sb['payment_status']) ?>"
                      data-id="<?= $sb['id'] ?>"
                      data-type="spa"
                      style="min-width:92px;display:inline-block;vertical-align:middle;"
                    >
                      <option value="unpaid" <?= $sb['payment_status']==='unpaid' ?'selected':'' ?>>Unpaid</option>
                      <option value="paid"   <?= $sb['payment_status']==='paid'   ?'selected':'' ?>>Paid</option>
                    </select>
                  </td>
                </tr>
                <tr id="spa-items-<?= (int)$sb['id'] ?>" class="spa-items-row" style="display:none;">
                  <td colspan="7" style="padding:0;">
                    <div class="spa-items-panel" style="background:#F5EFE4; border-top:1px solid rgba(139,111,46,0.15); padding:20px 24px;">
                      <div class="spa-items-loading" style="text-align:center; padding:24px; color:var(--muted); font-size:0.9rem;">
                        ⏳ Loading booked services…
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

</main>

<!-- ── Details Modal ────────────────────────────────────────── -->
<div id="modalBackdrop" class="modal-backdrop">
  <div id="detailModal" class="modal">
    <div class="modal__header">
      <h2 class="modal__title">Booking <span id="mRef"></span></h2>
      <button class="modal__close" onclick="closeDetail()">&#215;</button>
    </div>
    <div class="modal__body">
      <div class="modal__grid">
        <div>
          <span class="modal__field-label">Guest</span>
          <div class="modal__field-value" id="mName"></div>
          <div class="modal__field-value" id="mEmail" style="color:var(--muted);font-size:0.82rem;"></div>
          <div class="modal__field-value" id="mPhone" style="color:var(--muted);font-size:0.82rem;"></div>
        </div>
        <div>
          <span class="modal__field-label">Booking Details</span>
          <div class="modal__field-value" id="mDivision"></div>
          <div class="modal__field-value" id="mService" style="color:var(--muted);font-size:0.85rem;"></div>
          <div class="modal__field-value" id="mDate" style="color:var(--muted);font-size:0.85rem;"></div>
          <div class="modal__field-value" id="mTime"  style="color:var(--muted);font-size:0.85rem;"></div>
        </div>
      </div>

      <div style="margin-bottom:24px;">
        <span class="modal__field-label">Guest Notes</span>
        <div id="mNotes" class="modal__notes-box"></div>
      </div>

      <div>
        <span class="modal__field-label">Admin Notes</span>
        <textarea id="mAdminNotes" class="modal__textarea" placeholder="Internal notes…"></textarea>
        <button id="mSaveBtn" class="modal__save-btn">Save Notes</button>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/dashboard.js"></script>
</body>
</html>
