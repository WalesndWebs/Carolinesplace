<?php
/**
 * Caroline's Place — Admin Spa Products Catalog
 */
session_start();
require_once __DIR__ . '/../api/db.php';

if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

$admin = $_SESSION['admin'];
$db = getDb();

// Fetch categories, services and options
$catStmt = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
$categories = $catStmt->fetchAll();

$svcStmt = $db->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC");
$services = $svcStmt->fetchAll();

$optStmt = $db->query("SELECT * FROM options ORDER BY sort_order ASC, id ASC");
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
$totalServices = 0;
foreach ($categories as $c) {
    $svcs = $servicesByCat[$c['id']] ?? [];
    $totalServices += count($svcs);
    $catData[] = [
        'cat' => $c,
        'services' => $svcs
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Spa Catalog Management — Caroline's Place Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    .admin-layout { max-width: 1240px; margin: 0 auto; padding: 24px; }
    .catalog-block { background:#fff; border-radius:14px; border:1px solid rgba(27,20,16,0.1); margin-bottom:28px; padding:24px; box-shadow:0 4px 16px rgba(0,0,0,0.02); }
    .catalog-block__header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; border-bottom:1px solid rgba(27,20,16,0.08); padding-bottom:14px; }
    .service-row { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid rgba(27,20,16,0.05); }
    .service-row:last-child { border-bottom:0; }
  </style>
</head>
<body style="background:var(--bg);">

<header style="background:#fff; border-bottom:1px solid rgba(27,20,16,0.1); padding:16px 24px;">
  <div style="max-width:1240px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:20px;">
      <a href="/" style="font-family:var(--font-serif); font-size:20px; font-weight:600; color:var(--fg);">Caroline's Place</a>
      <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; background:var(--primary); color:#fff; padding:3px 10px; border-radius:999px;">Admin</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px; font-size:14px; flex-wrap:wrap;">
      <a href="/admin/dashboard.php" style="color:var(--muted);">Bookings</a>
      <a href="/admin/spa_products.php" style="font-weight:600; color:var(--primary);">Spa Catalog</a>
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
      <h1 style="font-family:var(--font-serif); font-size:28px; color:var(--fg); margin:0;">Spa Catalog</h1>
      <p style="color:var(--muted); font-size:14px; margin:4px 0 0;"><?php echo $totalServices; ?> services configured across <?php echo count($catData); ?> categories</p>
    </div>
    <a href="/spa_menu.php" class="btn btn--outline" target="_blank" style="padding:10px 18px; font-size:13px;">Preview Public Menu ↗</a>
  </div>

  <?php foreach ($catData as $cd): ?>
    <div class="catalog-block">
      <div class="catalog-block__header">
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="font-size:24px;"><?php echo $cd['cat']['icon'] ?: '✨'; ?></span>
          <div>
            <h2 style="font-family:var(--font-serif); font-size:20px; color:var(--fg); margin:0;"><?php echo htmlspecialchars($cd['cat']['name']); ?></h2>
            <span style="font-size:12px; color:var(--muted);"><?php echo count($cd['services']); ?> services</span>
          </div>
        </div>
      </div>

      <div>
        <?php foreach ($cd['services'] as $s): ?>
          <div class="service-row">
            <div>
              <div style="font-weight:600; color:var(--fg); font-size:15px;"><?php echo htmlspecialchars($s['name']); ?></div>
              <?php if (!empty($s['description'])): ?>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;"><?php echo htmlspecialchars($s['description']); ?></div>
              <?php endif; ?>
              <?php if (!empty($s['options'])): ?>
                <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:6px;">
                  <?php foreach ($s['options'] as $o): ?>
                    <span style="background:#faf5eb; border:1px solid rgba(27,20,16,0.08); padding:2px 8px; border-radius:4px; font-size:11px; color:var(--fg);">
                      <?php echo htmlspecialchars($o['option_label']); ?>: <strong style="color:var(--primary);"><?php echo priceFmt($o['price_ngn']); ?></strong>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
            <div style="text-align:right;">
              <span style="font-size:11px; padding:3px 8px; border-radius:999px; background:#e6f4ea; color:#137333; font-weight:600;">Active</span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>

</body>
</html>
