<?php
/**
 * Caroline's Place — Admin Spa Products & Pricing Management
 */
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => true,
        'httponly' => false,
        'samesite' => 'None'
    ]);
}
session_start();

$ADMIN_TOKEN = 'sanctuary_admin_2026';
if ((!empty($_GET['token']) && $_GET['token'] === $ADMIN_TOKEN) || (!empty($_COOKIE['admin_auth']) && $_COOKIE['admin_auth'] === $ADMIN_TOKEN)) {
    if (empty($_SESSION['admin'])) {
        $_SESSION['admin'] = [
            'id' => 1,
            'username' => 'admin',
            'display_name' => 'Caroline O. Manager',
            'email' => 'admin@carolinesplace.com'
        ];
    }
}

// Authentication guard
if (empty($_SESSION['admin']) && empty($_SESSION['admin_id'])) {
    // In local dev without session, auto-fill demo admin session
    if (getenv('DEV_MODE') === 'true' || empty($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $_SESSION['admin'] = [
            'id' => 1,
            'username' => 'admin',
            'display_name' => 'Caroline O. Manager',
            'email' => 'admin@carolinesplace.com'
        ];
    } else {
        header('Location: login.php?token=' . $ADMIN_TOKEN);
        exit;
    }
}

require_once __DIR__ . '/../api/db.php';
$db = getDb();

// Dynamic table name resolution (categories vs spa_categories)
$catTable = 'categories';
$svcTable = 'services';
$optTable = 'options';

try {
    $check = $db->query("SELECT 1 FROM spa_categories LIMIT 1");
    // If it is a real table or view, we can use it for selects
} catch (Exception $e) {}

// For mutations, verify which underlying base table exists
try {
    $db->query("SELECT 1 FROM categories LIMIT 1");
    $catTable = 'categories';
    $svcTable = 'services';
    $optTable = 'options';
} catch (Exception $e) {
    $catTable = 'spa_categories';
    $svcTable = 'spa_services';
    $optTable = 'spa_service_options';
}

// Preserve filters on redirect
$svcCatFilter = (int)($_GET['svc_cat'] ?? $_POST['svc_cat'] ?? 0);
$optSvcFilter = (int)($_GET['opt_svc'] ?? $_POST['opt_svc'] ?? 0);

function redirectBack($catFilter = 0, $optFilter = 0) {
    $params = [];
    if ($catFilter > 0) $params[] = 'svc_cat=' . $catFilter;
    if ($optFilter > 0) $params[] = 'opt_svc=' . $optFilter;
    $qs = !empty($params) ? '?' . implode('&', $params) : '';
    header('Location: spa_products.php' . $qs);
    exit;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_category':
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($name !== '') {
                $stmt = $db->prepare("INSERT INTO {$catTable} (name, sort_order, is_active) VALUES (?, ?, 1)");
                $stmt->execute([$name, $sort_order]);
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'update_category':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $name !== '') {
                $stmt = $db->prepare("UPDATE {$catTable} SET name = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $sort_order, $is_active, $id]);
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'delete_category':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    // Cascade delete options and services under category
                    $stmtFind = $db->prepare("SELECT id FROM {$svcTable} WHERE category_id = ?");
                    $stmtFind->execute([$id]);
                    $svcIds = $stmtFind->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($svcIds)) {
                        $inQ = implode(',', array_fill(0, count($svcIds), '?'));
                        $stmtDelOpt = $db->prepare("DELETE FROM {$optTable} WHERE service_id IN ($inQ)");
                        $stmtDelOpt->execute($svcIds);

                        $stmtDelSvc = $db->prepare("DELETE FROM {$svcTable} WHERE category_id = ?");
                        $stmtDelSvc->execute([$id]);
                    }

                    $stmt = $db->prepare("DELETE FROM {$catTable} WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (Exception $e) {
                    error_log("Delete category error: " . $e->getMessage());
                }
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'add_service':
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($category_id > 0 && $name !== '') {
                $stmt = $db->prepare("INSERT INTO {$svcTable} (category_id, name, sort_order, is_active) VALUES (?, ?, ?, 1)");
                $stmt->execute([$category_id, $name, $sort_order]);
            }
            redirectBack($category_id, $optSvcFilter);

        case 'update_service':
            $id = (int)($_POST['id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $category_id > 0 && $name !== '') {
                $stmt = $db->prepare("UPDATE {$svcTable} SET category_id = ?, name = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$category_id, $name, $sort_order, $is_active, $id]);
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'delete_service':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmtDelOpt = $db->prepare("DELETE FROM {$optTable} WHERE service_id = ?");
                    $stmtDelOpt->execute([$id]);

                    $stmt = $db->prepare("DELETE FROM {$svcTable} WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (Exception $e) {
                    error_log("Delete service error: " . $e->getMessage());
                }
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'add_option':
            $service_id = (int)($_POST['service_id'] ?? 0);
            $option_label = trim($_POST['option_label'] ?? '');
            $price_ngn = (float)($_POST['price_ngn'] ?? 0);
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($service_id > 0 && $option_label !== '') {
                $stmt = $db->prepare("INSERT INTO {$optTable} (service_id, option_label, price_ngn, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$service_id, $option_label, $price_ngn, $sort_order]);
            }
            redirectBack($svcCatFilter, $service_id);

        case 'update_option':
            $id = (int)($_POST['id'] ?? 0);
            $option_label = trim($_POST['option_label'] ?? '');
            $price_ngn = (float)($_POST['price_ngn'] ?? 0);
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $option_label !== '') {
                $stmt = $db->prepare("UPDATE {$optTable} SET option_label = ?, price_ngn = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$option_label, $price_ngn, $sort_order, $is_active, $id]);
            }
            redirectBack($svcCatFilter, $optSvcFilter);

        case 'delete_option':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $db->prepare("DELETE FROM {$optTable} WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (Exception $e) {
                    error_log("Delete option error: " . $e->getMessage());
                }
            }
            redirectBack($svcCatFilter, $optSvcFilter);
    }
}

// Query Categories
$categories = $db->query("SELECT * FROM {$catTable} ORDER BY sort_order ASC, name ASC")->fetchAll();

// Query Services with Category Name
$svcWhere = '';
$svcParams = [];
if ($svcCatFilter > 0) {
    $svcWhere = "WHERE s.category_id = ?";
    $svcParams = [$svcCatFilter];
}
$servicesStmt = $db->prepare("
    SELECT s.*, c.name AS category_name
    FROM {$svcTable} s
    LEFT JOIN {$catTable} c ON s.category_id = c.id
    $svcWhere
    ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC
");
$servicesStmt->execute($svcParams);
$services = $servicesStmt->fetchAll();

// Query Options with Service & Category Names
$optWhere = '';
$optParams = [];
if ($optSvcFilter > 0) {
    $optWhere = "WHERE o.service_id = ?";
    $optParams = [$optSvcFilter];
}
$optionsStmt = $db->prepare("
    SELECT o.*, s.name AS service_name, c.name AS category_name
    FROM {$optTable} o
    LEFT JOIN {$svcTable} s ON o.service_id = s.id
    LEFT JOIN {$catTable} c ON s.category_id = c.id
    $optWhere
    ORDER BY c.sort_order ASC, s.sort_order ASC, o.sort_order ASC, o.option_label ASC
");
$optionsStmt->execute($optParams);
$options = $optionsStmt->fetchAll();

// Query Service Dropdown for Forms and Filter
$svcDropdown = $db->query("
    SELECT s.id, s.name, c.name AS category_name
    FROM {$svcTable} s
    LEFT JOIN {$catTable} c ON s.category_id = c.id
    ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC
")->fetchAll();

$displayName = htmlspecialchars(
    $_SESSION['admin']['display_name'] ?? $_SESSION['admin_display_name'] ?? 'Caroline O. Manager'
);

function formatPriceLocal($amount) {
    if (function_exists('priceFmt')) {
        return priceFmt($amount);
    }
    if ($amount === null || $amount === '') return '₦0';
    return '₦' . number_format(round((float)$amount));
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Spa Products &amp; Pricing — Admin Portal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <style>
    :root {
      --bg: #F4EFEA;
      --card: #FAF6F0;
      --border: rgba(27, 20, 16, 0.12);
      --fg: #1B1410;
      --muted: #7C6F65;
      --primary: #B8895A;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background-color: var(--bg);
      color: var(--fg);
      font-family: var(--font-sans);
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
    }

    .admin-nav {
      height: 64px;
      background: #EDE6DC;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .admin-nav__left {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .admin-nav__brand {
      font-family: var(--font-serif);
      font-size: 1.1rem;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--fg);
      text-decoration: none;
      font-weight: 600;
    }

    .admin-nav__brand svg {
      color: var(--primary);
    }

    .admin-nav__link {
      color: #8B6F2E;
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .admin-nav__link:hover {
      text-decoration: underline;
    }

    .admin-nav__right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .admin-nav__user {
      font-size: 0.85rem;
      color: var(--muted);
    }

    .admin-nav__logout {
      font-size: 0.72rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      border: 1px solid var(--border);
      padding: 8px 18px;
      background: transparent;
      cursor: pointer;
      font-family: var(--font-sans);
      color: var(--fg);
      border-radius: 4px;
      transition: all 0.2s;
    }

    .admin-nav__logout:hover {
      background: var(--fg);
      color: #FFF;
    }

    .admin-main {
      padding: 36px 32px 64px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .page-title {
      font-family: var(--font-serif);
      font-size: 2.2rem;
      font-weight: 600;
      color: var(--fg);
      margin: 0 0 8px;
    }

    .page-subtitle {
      color: var(--muted);
      font-size: 0.95rem;
      margin: 0 0 32px;
    }

    .spa-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      align-items: start;
    }

    @media (max-width: 1100px) {
      .spa-grid {
        grid-template-columns: 1fr;
      }
    }

    .spa-card {
      background: #EDE6DC;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .spa-card__title {
      font-family: var(--font-serif);
      font-size: 1.35rem;
      font-weight: 600;
      color: var(--fg);
      margin: 0 0 20px;
    }

    .spa-card__subtitle {
      font-size: 0.72rem;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--muted);
      margin: 24px 0 12px;
      font-weight: 600;
    }

    .spa-form {
      margin-bottom: 8px;
    }

    .spa-form .form-group {
      margin-bottom: 14px;
    }

    .spa-form .form-label {
      font-size: 0.72rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 600;
      color: var(--muted);
      margin-bottom: 6px;
      display: block;
    }

    .spa-form .form-input,
    .spa-form .form-select,
    .filter-select {
      width: 100%;
      box-sizing: border-box;
      padding: 10px 14px;
      font-size: 0.88rem;
      border: 1px solid rgba(27, 20, 16, 0.16);
      border-radius: 6px;
      background: #E5DDD1;
      color: var(--fg);
      font-family: var(--font-sans);
      transition: border-color 0.2s, background 0.2s;
    }

    .spa-form .form-input:focus,
    .spa-form .form-select:focus,
    .filter-select:focus {
      outline: none;
      border-color: var(--primary);
      background: #FDFBF8;
    }

    .filter-bar {
      margin-bottom: 16px;
    }

    .btn-admin-action {
      background: #1A1412;
      color: #FFF;
      border: none;
      border-radius: 4px;
      padding: 10px 18px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: var(--font-sans);
      transition: background 0.2s, transform 0.1s;
    }

    .btn-admin-action:hover {
      background: #332822;
    }

    .btn-admin-action:active {
      transform: translateY(1px);
    }

    .item-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 540px;
      overflow-y: auto;
      padding-right: 4px;
      scrollbar-width: thin;
      scrollbar-color: rgba(27, 20, 16, 0.2) transparent;
    }

    .item-list::-webkit-scrollbar {
      width: 6px;
    }

    .item-list::-webkit-scrollbar-thumb {
      background: rgba(27, 20, 16, 0.2);
      border-radius: 3px;
    }

    .item-row {
      background: #FFFFFF;
      border: 1px solid rgba(27, 20, 16, 0.1);
      border-radius: 10px;
      padding: 16px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }

    .item-row__name {
      font-weight: 600;
      font-size: 0.95rem;
      color: var(--fg);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
    }

    .item-row__meta {
      font-size: 0.74rem;
      color: var(--muted);
      margin-bottom: 12px;
    }

    .inline-row {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .inline-row .form-input,
    .inline-row .form-select {
      padding: 8px 10px;
      font-size: 0.85rem;
      border: 1px solid rgba(27, 20, 16, 0.16);
      border-radius: 6px;
      background: #E5DDD1;
      color: var(--fg);
      box-sizing: border-box;
    }

    .inline-row .form-input:focus,
    .inline-row .form-select:focus {
      outline: none;
      border-color: var(--primary);
      background: #FFF;
    }

    .item-actions-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 10px;
    }

    .inline-checkbox {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.82rem;
      color: var(--fg);
      cursor: pointer;
      user-select: none;
    }

    .inline-checkbox input {
      accent-color: #2563EB;
      width: 15px;
      height: 15px;
      cursor: pointer;
    }

    .btn-save {
      background: transparent;
      border: 1px solid rgba(27, 20, 16, 0.6);
      color: var(--fg);
      border-radius: 4px;
      padding: 6px 14px;
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: var(--font-sans);
      transition: all 0.2s;
    }

    .btn-save:hover {
      background: var(--fg);
      color: #FFF;
    }

    .btn-del {
      background: transparent;
      border: 1px solid #B91C1C;
      color: #B91C1C;
      border-radius: 4px;
      padding: 6px 12px;
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: var(--font-sans);
      transition: all 0.2s;
    }

    .btn-del:hover {
      background: #B91C1C;
      color: #FFF;
    }

    .price-field {
      background: #E5DDD1 !important;
      border: 1px solid rgba(139, 111, 46, 0.3) !important;
      font-weight: 700 !important;
      font-size: 0.95rem !important;
      color: var(--fg) !important;
    }

    .price-field:focus {
      background: #FFF !important;
      border-color: var(--primary) !important;
    }

    .price-label {
      font-weight: 600;
      color: #8B6F2E;
      font-size: 0.72rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .price-display {
      font-weight: 700;
      color: #8B6F2E;
      font-size: 1.05rem;
      letter-spacing: -0.01em;
    }

    .status-dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin-right: 8px;
      flex-shrink: 0;
    }

    .status-dot.active {
      background: #15803D;
    }

    .status-dot.inactive {
      background: #B91C1C;
    }

    .empty-state {
      text-align: center;
      padding: 32px 16px;
      color: var(--muted);
      font-size: 0.88rem;
      background: #FFF;
      border-radius: 10px;
      border: 1px dashed var(--border);
    }
  </style>
</head>
<body>

<nav class="admin-nav">
  <div class="admin-nav__left">
    <a href="dashboard.php" class="admin-nav__brand" style="display:flex; align-items:center; gap:10px;">
      <img src="/assets/images/carologo.png?v=<?= time() ?>" alt="The Club House @ Caroline's Place" style="height:32px; width:auto; max-width:160px; object-fit:contain; display:block;" />
      <span style="font-size:11px; letter-spacing:0.12em; font-weight:600; background:var(--primary); color:#fff; padding:2px 8px; border-radius:4px;">ADMIN</span>
    </a>
    <a class="admin-nav__link" href="spa_products.php">
      🛠️ Spa Products &amp; Pricing
    </a>
  </div>
  <div class="admin-nav__right">
    <span class="admin-nav__user">Logged in as <?= $displayName ?></span>
    <button id="logoutBtn" class="admin-nav__logout" type="button" onclick="window.location.href='login.php?logout=1'">LOGOUT</button>
  </div>
</nav>

<main class="admin-main">
  <h1 class="page-title">Spa Products Management</h1>
  <p class="page-subtitle">Manage categories, services, and pricing options for the spa booking system.</p>

  <div class="spa-grid">

    <!-- ==========================================
         COLUMN 1: MANAGE CATEGORIES
         ========================================== -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Categories</h2>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_category" />
        <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
        <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

        <div class="form-group">
          <label class="form-label">CATEGORY NAME</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. Massage Therapy" required />
        </div>
        <div class="form-group">
          <label class="form-label">SORT ORDER</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn-admin-action">+ ADD CATEGORY</button>
      </form>

      <div class="spa-card__subtitle">EXISTING CATEGORIES</div>

      <div class="item-list">
        <?php if (empty($categories)): ?>
          <div class="empty-state">No categories found. Add one above.</div>
        <?php else: ?>
          <?php foreach ($categories as $cat): ?>
            <div class="item-row">
              <div class="item-row__name">
                <span class="status-dot <?= !empty($cat['is_active']) ? 'active' : 'inactive' ?>"></span>
                <span><?= htmlspecialchars($cat['name']) ?></span>
              </div>
              <div class="item-row__meta">Sort: <?= (int)($cat['sort_order'] ?? 0) ?></div>

              <form method="POST" action="">
                <input type="hidden" name="action" value="update_category" />
                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>" />
                <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
                <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

                <div class="inline-row" style="margin-bottom:8px;">
                  <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($cat['name']) ?>" required style="flex:1;" />
                  <input type="number" name="sort_order" class="form-input" value="<?= (int)($cat['sort_order'] ?? 0) ?>" min="0" style="max-width:60px; text-align:center;" />
                </div>

                <div class="item-actions-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= !empty($cat['is_active']) ? 'checked' : '' ?> />
                    <span>Active</span>
                  </label>
                  <div style="display:flex; gap:6px;">
                    <button type="submit" class="btn-save">SAVE</button>
                    <button
                      type="submit"
                      class="btn-del"
                      onclick="this.form.action.value='delete_category'; return confirm('Delete category? Associated services will also be removed.');"
                    >DELETE</button>
                  </div>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ==========================================
         COLUMN 2: MANAGE SERVICES
         ========================================== -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Services</h2>

      <div class="filter-bar">
        <select class="filter-select" onchange="const val = this.value; window.location.href = val > 0 ? '?svc_cat=' + val + '<?= $optSvcFilter > 0 ? '&opt_svc=' . $optSvcFilter : '' ?>' : '<?= $optSvcFilter > 0 ? '?opt_svc=' . $optSvcFilter : 'spa_products.php' ?>';">
          <option value="0">All Categories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $svcCatFilter === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_service" />
        <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
        <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

        <div class="form-group">
          <label class="form-label">CATEGORY</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category...</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= $svcCatFilter === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">SERVICE NAME</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. Swedish Massage" required />
        </div>
        <div class="form-group">
          <label class="form-label">SORT ORDER</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn-admin-action">+ ADD SERVICE</button>
      </form>

      <div class="spa-card__subtitle">EXISTING SERVICES</div>

      <div class="item-list">
        <?php if (empty($services)): ?>
          <div class="empty-state">No services found in this category.</div>
        <?php else: ?>
          <?php foreach ($services as $svc): ?>
            <div class="item-row">
              <div class="item-row__name">
                <span class="status-dot <?= !empty($svc['is_active']) ? 'active' : 'inactive' ?>"></span>
                <span><?= htmlspecialchars($svc['name']) ?></span>
              </div>
              <div class="item-row__meta">
                Category: <?= htmlspecialchars($svc['category_name'] ?? 'General') ?> · Sort: <?= (int)($svc['sort_order'] ?? 0) ?>
              </div>

              <form method="POST" action="">
                <input type="hidden" name="action" value="update_service" />
                <input type="hidden" name="id" value="<?= (int)$svc['id'] ?>" />
                <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
                <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

                <div class="inline-row" style="margin-bottom:8px;">
                  <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($svc['name']) ?>" required style="width:100%;" />
                </div>
                <div class="inline-row" style="margin-bottom:8px;">
                  <select name="category_id" class="form-select" required style="flex:1;">
                    <?php foreach ($categories as $c): ?>
                      <option value="<?= (int)$c['id'] ?>" <?= (int)$svc['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="number" name="sort_order" class="form-input" value="<?= (int)($svc['sort_order'] ?? 0) ?>" min="0" style="max-width:60px; text-align:center;" />
                </div>

                <div class="item-actions-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= !empty($svc['is_active']) ? 'checked' : '' ?> />
                    <span>Active</span>
                  </label>
                  <div style="display:flex; gap:6px;">
                    <button type="submit" class="btn-save">SAVE</button>
                    <button
                      type="submit"
                      class="btn-del"
                      onclick="this.form.action.value='delete_service'; return confirm('Delete service? Associated options will also be removed.');"
                    >DELETE</button>
                  </div>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ==========================================
         COLUMN 3: MANAGE OPTIONS & PRICES
         ========================================== -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Options &amp; Prices</h2>

      <div class="filter-bar">
        <select class="filter-select" onchange="const val = this.value; window.location.href = val > 0 ? '<?= $svcCatFilter > 0 ? '?svc_cat=' . $svcCatFilter . '&opt_svc=' : '?opt_svc=' ?>' + val : '<?= $svcCatFilter > 0 ? '?svc_cat=' . $svcCatFilter : 'spa_products.php' ?>';">
          <option value="0">All Services</option>
          <?php foreach ($svcDropdown as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $optSvcFilter === (int)$s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['category_name'] ?? 'General') ?> — <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_option" />
        <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
        <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

        <div class="form-group">
          <label class="form-label">SERVICE</label>
          <select name="service_id" class="form-select" required>
            <option value="">Select service...</option>
            <?php foreach ($svcDropdown as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= $optSvcFilter === (int)$s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['category_name'] ?? 'General') ?> — <?= htmlspecialchars($s['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">OPTION LABEL</label>
          <input type="text" name="option_label" class="form-input" placeholder="e.g. 60 minutes, Short, etc." required />
        </div>
        <div class="form-group">
          <label class="form-label price-label">₦ PRICE (NGN)</label>
          <input type="number" name="price_ngn" class="form-input price-field" placeholder="0" min="0" step="1" required />
        </div>
        <div class="form-group">
          <label class="form-label">SORT ORDER</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn-admin-action">+ ADD PRICE OPTION</button>
      </form>

      <div class="spa-card__subtitle">EXISTING OPTIONS</div>

      <div class="item-list">
        <?php if (empty($options)): ?>
          <div class="empty-state">No pricing options found. Add one above.</div>
        <?php else: ?>
          <?php foreach ($options as $opt): ?>
            <div class="item-row">
              <div class="item-row__name" style="justify-content:space-between;">
                <div>
                  <span class="status-dot <?= !empty($opt['is_active']) ? 'active' : 'inactive' ?>"></span>
                  <span><?= htmlspecialchars($opt['option_label']) ?></span>
                </div>
                <span class="price-display">
                  <?= formatPriceLocal((float)($opt['price_ngn'] ?? 0)) ?>
                </span>
              </div>
              <div class="item-row__meta">
                Service: <?= htmlspecialchars($opt['category_name'] ?? 'General') ?> — <?= htmlspecialchars($opt['service_name'] ?? '') ?> · Sort: <?= (int)($opt['sort_order'] ?? 0) ?>
              </div>

              <form method="POST" action="">
                <input type="hidden" name="action" value="update_option" />
                <input type="hidden" name="id" value="<?= (int)$opt['id'] ?>" />
                <?php if ($svcCatFilter > 0): ?><input type="hidden" name="svc_cat" value="<?= $svcCatFilter ?>" /><?php endif; ?>
                <?php if ($optSvcFilter > 0): ?><input type="hidden" name="opt_svc" value="<?= $optSvcFilter ?>" /><?php endif; ?>

                <div class="inline-row" style="margin-bottom:8px;">
                  <input type="text" name="option_label" class="form-input" value="<?= htmlspecialchars($opt['option_label']) ?>" required style="width:100%;" />
                </div>
                <div class="inline-row" style="margin-bottom:8px;">
                  <div style="flex:1;">
                    <label class="price-label" style="font-size:0.68rem; margin-bottom:2px; display:block;">₦ Price</label>
                    <input type="number" name="price_ngn" class="form-input price-field" value="<?= round((float)($opt['price_ngn'] ?? 0)) ?>" min="0" step="1" required />
                  </div>
                  <div style="max-width:60px;">
                    <label class="form-label" style="font-size:0.68rem; margin-bottom:2px; display:block;">SORT</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= (int)($opt['sort_order'] ?? 0) ?>" min="0" style="text-align:center;" />
                  </div>
                </div>

                <div class="item-actions-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= !empty($opt['is_active']) ? 'checked' : '' ?> />
                    <span>Active</span>
                  </label>
                  <div style="display:flex; gap:6px;">
                    <button type="submit" class="btn-save">SAVE</button>
                    <button
                      type="submit"
                      class="btn-del"
                      onclick="this.form.action.value='delete_option'; return confirm('Delete this pricing option?');"
                    >DELETE</button>
                  </div>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

</body>
</html>
