<?php
/**
 * Caroline's Place — Admin Bookings Dashboard
 */
session_start();
require_once __DIR__ . '/../api/db.php';

if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

$admin = $_SESSION['admin'];
$db = getDb();

$currentStatus = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$currentDiv    = isset($_GET['division']) ? trim($_GET['division']) : 'all';

// Fetch stats
$stats = [
    'total'     => (int)$db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'pending'   => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
    'confirmed' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
    'completed' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn(),
    'cancelled' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn(),
    'revenue'   => (float)$db->query("SELECT COALESCE(SUM(total_amount_ngn), 0) FROM bookings WHERE status != 'cancelled'")->fetchColumn(),
];

// Query bookings with filter
$sql = "SELECT b.*, (SELECT COUNT(*) FROM booking_items bi WHERE bi.booking_id = b.id) AS item_count 
        FROM bookings b WHERE 1=1";
$params = [];

if ($currentStatus !== 'all' && in_array($currentStatus, ['pending', 'confirmed', 'completed', 'cancelled'], true)) {
    $sql .= " AND b.status = ?";
    $params[] = $currentStatus;
}

if ($currentDiv !== 'all' && !empty($currentDiv)) {
    $sql .= " AND b.division = ?";
    $params[] = $currentDiv;
}

$sql .= " ORDER BY b.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — Caroline's Place</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    .admin-layout { max-width: 1240px; margin: 0 auto; padding: 24px; }
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid rgba(27,20,16,0.1); }
    .stat-card__lbl { font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); font-weight: 600; margin-bottom: 6px; }
    .stat-card__val { font-family: var(--font-serif); font-size: 26px; font-weight: 700; color: var(--fg); }
    .badge-status { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-confirmed { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .table-container { background: #fff; border-radius: 14px; border: 1px solid rgba(27,20,16,0.1); overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    .admin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .admin-table th { background: #faf6ef; text-align: left; padding: 14px 16px; font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); font-weight: 600; border-bottom: 1px solid rgba(27,20,16,0.1); }
    .admin-table td { padding: 14px 16px; border-bottom: 1px solid rgba(27,20,16,0.06); vertical-align: middle; }
    .admin-table tr:hover td { background: #fffcf8; }
    .action-btn { background: #f3f4f6; border: 1px solid rgba(27,20,16,0.1); border-radius: 6px; padding: 5px 10px; font-size: 12px; cursor: pointer; color: var(--fg); transition: all .15s ease; }
    .action-btn:hover { background: #e5e7eb; }
    .action-btn--active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); z-index: 999; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 560px; padding: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto; }
  </style>
</head>
<body style="background:var(--bg);">

<!-- Admin Nav Bar -->
<header style="background:#fff; border-bottom:1px solid rgba(27,20,16,0.1); padding:16px 24px;">
  <div style="max-width:1240px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:16px;">
      <a href="/" style="font-family:var(--font-serif); font-size:20px; font-weight:600; color:var(--fg);">Caroline's Place</a>
      <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; background:var(--primary); color:#fff; padding:3px 10px; border-radius:999px;">Admin</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px; font-size:14px; flex-wrap:wrap;">
      <a href="/admin/dashboard.php" style="font-weight:600; color:var(--primary);">Bookings</a>
      <a href="/admin/spa_products.php" style="color:var(--muted);">Spa Catalog</a>
      <a href="/" style="color:var(--muted);">Public Site</a>
      <span style="color:var(--muted);">|</span>
      <span style="color:var(--fg); font-weight:500;">Hello, <?php echo htmlspecialchars($admin['display_name'] ?: $admin['username']); ?></span>
      <a href="/api/logout.php" style="color:#c53030; text-decoration:none; font-size:13px; padding:6px 12px; border:1px solid rgba(197,48,48,0.3); border-radius:6px;">Logout</a>
    </div>
  </div>
</header>

<div class="admin-layout">

  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
      <h1 style="font-family:var(--font-serif); font-size:28px; color:var(--fg); margin:0;">Spa Bookings Dashboard</h1>
      <p style="color:var(--muted); font-size:14px; margin:4px 0 0;">Manage reservations, view itemized treatment breakdowns, and track guest requests.</p>
    </div>
    <div style="display:flex; gap:10px;">
      <button type="button" id="refreshBtn" class="action-btn">⟳ Refresh</button>
      <a href="/spa_menu.php" class="btn btn--primary" style="padding:8px 16px; font-size:13px;" target="_blank">+ New Booking</a>
    </div>
  </div>

  <!-- Stat Cards -->
  <div class="stat-cards">
    <div class="stat-card">
      <div class="stat-card__lbl">Total Bookings</div>
      <div class="stat-card__val" id="statTotal"><?php echo $stats['total']; ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__lbl">Pending</div>
      <div class="stat-card__val" id="statPending" style="color:#d97706;"><?php echo $stats['pending']; ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__lbl">Confirmed</div>
      <div class="stat-card__val" id="statConfirmed" style="color:#2563eb;"><?php echo $stats['confirmed']; ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__lbl">Completed</div>
      <div class="stat-card__val" id="statCompleted" style="color:#059669;"><?php echo $stats['completed']; ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-card__lbl">Total Revenue</div>
      <div class="stat-card__val" style="color:var(--primary);"><?php echo priceFmt($stats['revenue']); ?></div>
    </div>
  </div>

  <!-- Status Filter Pills -->
  <div style="display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap;">
    <a href="/admin/dashboard.php" style="padding:6px 14px; border-radius:999px; font-size:13px; text-decoration:none; <?php echo $currentStatus === 'all' ? 'background:var(--primary); color:#fff; font-weight:600;' : 'background:#fff; border:1px solid rgba(27,20,16,0.1); color:var(--muted);'; ?>">All (<?php echo $stats['total']; ?>)</a>
    <a href="/admin/dashboard.php?status=pending" style="padding:6px 14px; border-radius:999px; font-size:13px; text-decoration:none; <?php echo $currentStatus === 'pending' ? 'background:#f59e0b; color:#fff; font-weight:600;' : 'background:#fff; border:1px solid rgba(27,20,16,0.1); color:var(--muted);'; ?>">Pending (<?php echo $stats['pending']; ?>)</a>
    <a href="/admin/dashboard.php?status=confirmed" style="padding:6px 14px; border-radius:999px; font-size:13px; text-decoration:none; <?php echo $currentStatus === 'confirmed' ? 'background:#3b82f6; color:#fff; font-weight:600;' : 'background:#fff; border:1px solid rgba(27,20,16,0.1); color:var(--muted);'; ?>">Confirmed (<?php echo $stats['confirmed']; ?>)</a>
    <a href="/admin/dashboard.php?status=completed" style="padding:6px 14px; border-radius:999px; font-size:13px; text-decoration:none; <?php echo $currentStatus === 'completed' ? 'background:#10b981; color:#fff; font-weight:600;' : 'background:#fff; border:1px solid rgba(27,20,16,0.1); color:var(--muted);'; ?>">Completed (<?php echo $stats['completed']; ?>)</a>
    <a href="/admin/dashboard.php?status=cancelled" style="padding:6px 14px; border-radius:999px; font-size:13px; text-decoration:none; <?php echo $currentStatus === 'cancelled' ? 'background:#ef4444; color:#fff; font-weight:600;' : 'background:#fff; border:1px solid rgba(27,20,16,0.1); color:var(--muted);'; ?>">Cancelled (<?php echo $stats['cancelled']; ?>)</a>
  </div>

  <!-- Bookings Table -->
  <div class="table-container">
    <?php if (empty($bookings)): ?>
      <div style="padding:48px; text-align:center; color:var(--muted);">
        <p style="font-size:16px; margin-bottom:12px;">No bookings found for the selected filter.</p>
        <a href="/spa_menu.php" class="btn btn--primary btn--sm" target="_blank">Create a booking</a>
      </div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Guest</th>
              <th>Date &amp; Time</th>
              <th>Services</th>
              <th>Total (NGN)</th>
              <th>Status</th>
              <th>Update Status</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td>
                  <a href="/confirmation.php?ref=<?php echo urlencode($b['reference_code']); ?>" style="font-family:monospace; font-weight:700; color:var(--primary); text-decoration:underline;" target="_blank">
                    <?php echo htmlspecialchars($b['reference_code']); ?>
                  </a>
                </td>
                <td>
                  <div style="font-weight:600; color:var(--fg);"><?php echo htmlspecialchars($b['full_name']); ?></div>
                  <div style="font-size:12px; color:var(--muted);"><?php echo htmlspecialchars($b['email']); ?> · <?php echo htmlspecialchars($b['phone']); ?></div>
                </td>
                <td>
                  <div><?php echo htmlspecialchars($b['preferred_date']); ?></div>
                  <div style="font-size:12px; color:var(--muted);"><?php echo htmlspecialchars($b['preferred_time']); ?></div>
                </td>
                <td>
                  <button type="button" class="action-btn spa-view-items-btn" data-id="<?php echo $b['id']; ?>">
                    🛒 <?php echo (int)$b['item_count']; ?> service(s)
                  </button>
                </td>
                <td style="font-family:var(--font-serif); font-weight:700; color:var(--fg); font-size:15px;">
                  <?php echo priceFmt($b['total_amount_ngn']); ?>
                </td>
                <td>
                  <span class="badge-status badge-<?php echo htmlspecialchars($b['status']); ?>">
                    <?php echo htmlspecialchars($b['status']); ?>
                  </span>
                </td>
                <td>
                  <form method="POST" action="/api/update-booking.php" style="display:inline-flex; gap:6px; align-items:center;">
                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>" />
                    <select name="status" class="status-select" data-id="<?php echo $b['id']; ?>" data-type="spa" style="padding:4px 8px; font-size:12px; border:1px solid rgba(27,20,16,0.15); border-radius:6px; background:#fff;">
                      <option value="pending" <?php echo $b['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="confirmed" <?php echo $b['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="completed" <?php echo $b['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $b['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <noscript>
                      <button type="submit" class="action-btn">Save</button>
                    </noscript>
                  </form>
                </td>
                <td>
                  <button type="button" class="action-btn" onclick="openDetail('<?php echo rawurlencode(json_encode($b)); ?>')">
                    View
                  </button>
                </td>
              </tr>
              <!-- Spa items accordion expansion row -->
              <tr id="spa-items-<?php echo $b['id']; ?>" style="display:none; background:#faf7f0;">
                <td colspan="8" style="padding:16px 20px;">
                  <div class="spa-items-panel" data-loaded="0">
                    <span class="spa-items-loading" style="color:var(--muted); font-size:13px;">Loading booked treatments…</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Details Modal -->
<div class="modal-backdrop" id="modalBackdrop">
  <div class="modal-card" id="detailModal">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(27,20,16,0.1); padding-bottom:12px;">
      <h3 style="margin:0; font-family:var(--font-serif); font-size:20px;">Reservation Details</h3>
      <button type="button" onclick="closeDetail()" style="background:none; border:none; font-size:20px; cursor:pointer;">✕</button>
    </div>
    <div style="font-size:14px; display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px;">
      <div><span style="color:var(--muted); font-size:11px; display:block;">REFERENCE</span><strong id="mRef"></strong></div>
      <div><span style="color:var(--muted); font-size:11px; display:block;">DIVISION</span><strong id="mDivision"></strong><strong id="mService" style="display:none;"></strong></div>
      <div><span style="color:var(--muted); font-size:11px; display:block;">GUEST</span><strong id="mName"></strong></div>
      <div><span style="color:var(--muted); font-size:11px; display:block;">CONTACT</span><strong id="mEmail"></strong><br><span id="mPhone"></span></div>
      <div><span style="color:var(--muted); font-size:11px; display:block;">DATE</span><strong id="mDate"></strong></div>
      <div><span style="color:var(--muted); font-size:11px; display:block;">TIME</span><strong id="mTime"></strong></div>
      <div style="grid-column:1 / -1;"><span style="color:var(--muted); font-size:11px; display:block;">SERVICE / NOTES</span><p id="mNotes" style="margin:4px 0 0; background:#f9f9f9; padding:8px 12px; border-radius:6px;"></p></div>
    </div>
    <div style="margin-top:16px;">
      <label for="mAdminNotes" style="font-size:12px; font-weight:600; display:block; margin-bottom:6px;">Internal Concierge Notes</label>
      <textarea id="mAdminNotes" style="width:100%; min-height:80px; padding:10px; border:1px solid rgba(27,20,16,0.15); border-radius:8px; font-family:inherit; font-size:13px;"></textarea>
      <div style="text-align:right; margin-top:12px;">
        <button type="button" id="mSaveBtn" class="btn btn--primary btn--sm">Save Notes</button>
      </div>
    </div>
  </div>
</div>

<script src="/assets/js/dashboard.js"></script>

</body>
</html>
