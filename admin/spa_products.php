<?php
session_start();

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/db.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_category':
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($name !== '') {
                $stmt = $db->prepare("INSERT INTO spa_categories (name, sort_order) VALUES (?, ?)");
                $stmt->execute([$name, $sort_order]);
            }
            header('Location: spa_products.php');
            exit;

        case 'update_category':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $name !== '') {
                $stmt = $db->prepare("UPDATE spa_categories SET name = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $sort_order, $is_active, $id]);
            }
            header('Location: spa_products.php');
            exit;

        case 'delete_category':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $check = $db->prepare("SELECT COUNT(*) FROM spa_services WHERE category_id = ?");
                    $check->execute([$id]);
                    $count = (int)$check->fetchColumn();
                    if ($count === 0) {
                        $stmt = $db->prepare("DELETE FROM spa_categories WHERE id = ?");
                        $stmt->execute([$id]);
                    }
                } catch (Exception $e) {
                }
            }
            header('Location: spa_products.php');
            exit;

        case 'add_service':
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($category_id > 0 && $name !== '') {
                $stmt = $db->prepare("INSERT INTO spa_services (category_id, name, sort_order) VALUES (?, ?, ?)");
                $stmt->execute([$category_id, $name, $sort_order]);
            }
            header('Location: spa_products.php');
            exit;

        case 'update_service':
            $id = (int)($_POST['id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $category_id > 0 && $name !== '') {
                $stmt = $db->prepare("UPDATE spa_services SET category_id = ?, name = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$category_id, $name, $sort_order, $is_active, $id]);
            }
            header('Location: spa_products.php');
            exit;

        case 'delete_service':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $db->prepare("DELETE FROM spa_services WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (Exception $e) {
                }
            }
            header('Location: spa_products.php');
            exit;

        case 'add_option':
            $service_id = (int)($_POST['service_id'] ?? 0);
            $option_label = trim($_POST['option_label'] ?? '');
            $price_ngn = (float)($_POST['price_ngn'] ?? 0);
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if ($service_id > 0 && $option_label !== '') {
                $stmt = $db->prepare("INSERT INTO spa_service_options (service_id, option_label, price_ngn, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$service_id, $option_label, $price_ngn, $sort_order]);
            }
            header('Location: spa_products.php');
            exit;

        case 'update_option':
            $id = (int)($_POST['id'] ?? 0);
            $option_label = trim($_POST['option_label'] ?? '');
            $price_ngn = (float)($_POST['price_ngn'] ?? 0);
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if ($id > 0 && $option_label !== '') {
                $stmt = $db->prepare("UPDATE spa_service_options SET option_label = ?, price_ngn = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$option_label, $price_ngn, $sort_order, $is_active, $id]);
            }
            header('Location: spa_products.php');
            exit;

        case 'delete_option':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $db->prepare("DELETE FROM spa_service_options WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (Exception $e) {
                }
            }
            header('Location: spa_products.php');
            exit;
    }
}

$categories = $db->query("SELECT * FROM spa_categories ORDER BY sort_order ASC, name ASC")->fetchAll();

$svcCatFilter = (int)($_GET['svc_cat'] ?? 0);
$svcWhere = '';
$svcParams = [];
if ($svcCatFilter > 0) {
    $svcWhere = "WHERE s.category_id = ?";
    $svcParams = [$svcCatFilter];
}
$services = $db->prepare("
    SELECT s.*, c.name AS category_name
    FROM spa_services s
    LEFT JOIN spa_categories c ON s.category_id = c.id
    $svcWhere
    ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC
");
$services->execute($svcParams);
$services = $services->fetchAll();

$optSvcFilter = (int)($_GET['opt_svc'] ?? 0);
$optWhere = '';
$optParams = [];
if ($optSvcFilter > 0) {
    $optWhere = "WHERE o.service_id = ?";
    $optParams = [$optSvcFilter];
}
$options = $db->prepare("
    SELECT o.*, s.name AS service_name, c.name AS category_name
    FROM spa_service_options o
    LEFT JOIN spa_services s ON o.service_id = s.id
    LEFT JOIN spa_categories c ON s.category_id = c.id
    $optWhere
    ORDER BY c.sort_order ASC, s.sort_order ASC, o.sort_order ASC, o.option_label ASC
");
$options->execute($optParams);
$options = $options->fetchAll();

$svcDropdown = $db->query("
    SELECT s.id, s.name, c.name AS category_name
    FROM spa_services s
    LEFT JOIN spa_categories c ON s.category_id = c.id
    ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC
")->fetchAll();

$displayName = htmlspecialchars($_SESSION['admin_display_name'] ?? 'Admin');

function priceFmtLocal(float|int|null $p): string {
    if (function_exists('priceFmt')) return priceFmt($p);
    if ($p === null) return 'TBD';
    return '₦' . number_format((int) round($p));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Spa Products — Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <style>
    .admin-nav__link { padding: 0 10px; }
    .spa-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      align-items: start;
    }
    @media (max-width: 1100px) {
      .spa-grid { grid-template-columns: 1fr; }
    }
    .spa-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 24px;
    }
    .spa-card__title {
      font-family: var(--font-serif);
      font-size: 1.3rem;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }
    .spa-card__subtitle {
      font-size: 0.75rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--muted);
      margin: 20px 0 10px;
      font-weight: 500;
    }
    .spa-form { margin-bottom: 24px; }
    .spa-form .form-group { margin-bottom: 12px; }
    .spa-form .form-label { font-size: 0.8rem; margin-bottom: 6px; }
    .spa-form .form-input { padding: 10px 14px; font-size: 0.85rem; }
    .spa-form .btn { padding: 12px 24px; font-size: 0.65rem; }

    .inline-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .inline-row .form-input { flex: 1; min-width: 80px; padding: 8px 10px; font-size: 0.8rem; }
    .inline-row .form-select { flex: 1; min-width: 80px; padding: 8px 10px; font-size: 0.8rem; }
    .inline-row .btn { padding: 8px 14px; font-size: 0.65rem; white-space: nowrap; }
    .inline-checkbox { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: var(--muted); }

    .item-list { display: flex; flex-direction: column; gap: 10px; max-height: 520px; overflow-y: auto; padding-right: 4px; }
    .item-row {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px;
    }
    .item-row__name { font-weight: 500; font-size: 0.9rem; margin-bottom: 8px; }
    .item-row__meta { font-size: 0.72rem; color: var(--muted); margin-bottom: 8px; }

    .price-field {
      background: rgba(139,111,46,0.06) !important;
      border: 1px solid rgba(139,111,46,0.25) !important;
      font-weight: 700 !important;
      font-size: 0.95rem !important;
    }
    .price-label {
      font-weight: 600;
      color: var(--primary);
      font-size: 0.8rem;
    }
    .price-display {
      font-weight: 700;
      color: var(--primary);
      font-size: 0.95rem;
    }
    .status-dot {
      display: inline-block;
      width: 8px; height: 8px;
      border-radius: 50%;
      margin-right: 6px;
    }
    .status-dot.active { background: #166534; }
    .status-dot.inactive { background: #b91c1c; }

    .filter-bar {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
      align-items: center;
    }
    .filter-bar .filter-select { padding: 8px 12px; font-size: 0.8rem; }

    .empty-state {
      text-align: center;
      padding: 24px;
      color: var(--muted);
      font-size: 0.85rem;
    }
  </style>
</head>
<body style="background:var(--bg);">

<nav class="admin-nav">
  <div class="admin-nav__brand">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--primary)"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    Admin
  </div>
  <a class="admin-nav__link" href="spa_products.php" style="margin-right:auto;color:var(--primary);text-decoration:none;font-weight:600;font-size:14px;">🛠️ Spa Products &amp; Pricing</a>
  <div class="admin-nav__right">
    <span class="admin-nav__user">Logged in as <?= $displayName ?></span>
    <button id="logoutBtn" class="admin-nav__logout">Logout</button>
  </div>
</nav>

<main class="admin-main">

  <h1 style="font-family:var(--font-serif);font-size:2rem;margin-bottom:8px;">Spa Products Management</h1>
  <p style="color:var(--muted);margin-bottom:28px;">Manage categories, services, and pricing options for the spa booking system.</p>

  <div class="spa-grid">

    <!-- CARD 1: Categories -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Categories</h2>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_category" />
        <div class="form-group">
          <label class="form-label">Category Name</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. Massage Therapy" required />
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn btn--primary">+ Add Category</button>
      </form>

      <div class="spa-card__subtitle">Existing Categories</div>

      <div class="item-list">
        <?php if (empty($categories)): ?>
          <div class="empty-state">No categories yet. Add one above.</div>
        <?php else: ?>
          <?php foreach ($categories as $cat): ?>
            <div class="item-row">
              <div class="item-row__name">
                <span class="status-dot <?= $cat['is_active'] ? 'active' : 'inactive' ?>"></span>
                <?= htmlspecialchars($cat['name']) ?>
              </div>
              <div class="item-row__meta">Sort: <?= (int)$cat['sort_order'] ?></div>
              <form method="POST" action="">
                <input type="hidden" name="action" value="update_category" />
                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>" />
                <div class="inline-row" style="margin-bottom:6px;">
                  <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($cat['name']) ?>" required />
                  <input type="number" name="sort_order" class="form-input" value="<?= (int)$cat['sort_order'] ?>" min="0" style="max-width:80px;" />
                </div>
                <div class="inline-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= $cat['is_active'] ? 'checked' : '' ?> />
                    Active
                  </label>
                  <button type="submit" class="btn btn--outline" style="margin-left:auto;">Save</button>
                  <button
                    type="submit"
                    class="btn btn--outline"
                    style="border-color:#b91c1c;color:#b91c1c;"
                    formmethod="POST"
                    onclick="this.form.action.value='delete_category';return confirm('Delete category? Cannot delete if it has services.');"
                  >Delete</button>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- CARD 2: Services -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Services</h2>

      <div class="filter-bar">
        <select class="filter-select" onchange="window.location.href='?svc_cat='+this.value+'<?= $optSvcFilter > 0 ? '&opt_svc='.$optSvcFilter : '' ?>'">
          <option value="0">All Categories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $svcCatFilter === (int)$c['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_service" />
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category…</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= $svcCatFilter === (int)$c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Service Name</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. Swedish Massage" required />
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn btn--primary">+ Add Service</button>
      </form>

      <div class="spa-card__subtitle">Existing Services</div>

      <div class="item-list">
        <?php if (empty($services)): ?>
          <div class="empty-state">No services found.</div>
        <?php else: ?>
          <?php foreach ($services as $svc): ?>
            <div class="item-row">
              <div class="item-row__name">
                <span class="status-dot <?= $svc['is_active'] ? 'active' : 'inactive' ?>"></span>
                <?= htmlspecialchars($svc['name']) ?>
              </div>
              <div class="item-row__meta">
                Category: <?= htmlspecialchars($svc['category_name']) ?> · Sort: <?= (int)$svc['sort_order'] ?>
              </div>
              <form method="POST" action="">
                <input type="hidden" name="action" value="update_service" />
                <input type="hidden" name="id" value="<?= (int)$svc['id'] ?>" />
                <div class="inline-row" style="margin-bottom:6px;">
                  <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($svc['name']) ?>" required />
                </div>
                <div class="inline-row" style="margin-bottom:6px;">
                  <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $c): ?>
                      <option value="<?= (int)$c['id'] ?>" <?= (int)$svc['category_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <input type="number" name="sort_order" class="form-input" value="<?= (int)$svc['sort_order'] ?>" min="0" style="max-width:80px;" />
                </div>
                <div class="inline-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= $svc['is_active'] ? 'checked' : '' ?> />
                    Active
                  </label>
                  <button type="submit" class="btn btn--outline" style="margin-left:auto;">Save</button>
                  <button
                    type="button"
                    class="btn btn--outline"
                    style="border-color:#b91c1c;color:#b91c1c;"
                    onclick="
                      if (confirm('Delete service? All options under it will be removed.')) {
                        var f = this.form;
                        f.action.value = 'delete_service';
                        f.submit();
                      }
                    "
                  >Delete</button>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- CARD 3: Options & Prices -->
    <div class="spa-card">
      <h2 class="spa-card__title">Manage Options &amp; Prices</h2>

      <div class="filter-bar">
        <select class="filter-select" onchange="window.location.href='<?= $svcCatFilter > 0 ? '?svc_cat='.$svcCatFilter.'&' : '?' ?>opt_svc='+this.value">
          <option value="0">All Services</option>
          <?php foreach ($svcDropdown as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $optSvcFilter === (int)$s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['category_name']) ?> — <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <form method="POST" action="" class="spa-form">
        <input type="hidden" name="action" value="add_option" />
        <div class="form-group">
          <label class="form-label">Service</label>
          <select name="service_id" class="form-select" required>
            <option value="">Select service…</option>
            <?php foreach ($svcDropdown as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= $optSvcFilter === (int)$s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['category_name']) ?> — <?= htmlspecialchars($s['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Option Label</label>
          <input type="text" name="option_label" class="form-input" placeholder="e.g. 60 minutes, Short, etc." required />
        </div>
        <div class="form-group">
          <label class="form-label price-label">₦ Price (NGN)</label>
          <input type="number" name="price_ngn" class="form-input price-field" placeholder="0" min="0" step="1" required />
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-input" value="0" min="0" />
        </div>
        <button type="submit" class="btn btn--primary">+ Add Price Option</button>
      </form>

      <div class="spa-card__subtitle">Existing Options</div>

      <div class="item-list">
        <?php if (empty($options)): ?>
          <div class="empty-state">No pricing options found.</div>
        <?php else: ?>
          <?php foreach ($options as $opt): ?>
            <div class="item-row">
              <div class="item-row__name">
                <span class="status-dot <?= $opt['is_active'] ? 'active' : 'inactive' ?>"></span>
                <?= htmlspecialchars($opt['option_label']) ?>
                <span class="price-display" style="float:right;">
                  <?= priceFmtLocal((float)$opt['price_ngn']) ?>
                </span>
              </div>
              <div class="item-row__meta">
                Service: <?= htmlspecialchars($opt['category_name']) ?> — <?= htmlspecialchars($opt['service_name']) ?> · Sort: <?= (int)$opt['sort_order'] ?>
              </div>
              <form method="POST" action="">
                <input type="hidden" name="action" value="update_option" />
                <input type="hidden" name="id" value="<?= (int)$opt['id'] ?>" />
                <div class="inline-row" style="margin-bottom:6px;">
                  <input type="text" name="option_label" class="form-input" value="<?= htmlspecialchars($opt['option_label']) ?>" required />
                </div>
                <div class="inline-row" style="margin-bottom:6px;">
                  <div style="flex:1;">
                    <label class="price-label" style="font-size:0.7rem;">₦ Price</label>
                    <input type="number" name="price_ngn" class="form-input price-field" value="<?= (float)$opt['price_ngn'] ?>" min="0" step="1" required />
                  </div>
                  <div style="max-width:80px;">
                    <label class="form-label" style="font-size:0.7rem;">Sort</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= (int)$opt['sort_order'] ?>" min="0" />
                  </div>
                </div>
                <div class="inline-row">
                  <label class="inline-checkbox">
                    <input type="checkbox" name="is_active" <?= $opt['is_active'] ? 'checked' : '' ?> />
                    Active
                  </label>
                  <button type="submit" class="btn btn--outline" style="margin-left:auto;">Save</button>
                  <button
                    type="button"
                    class="btn btn--outline"
                    style="border-color:#b91c1c;color:#b91c1c;"
                    onclick="
                      if (confirm('Delete this pricing option?')) {
                        var f = this.form;
                        f.action.value = 'delete_option';
                        f.submit();
                      }
                    "
                  >Delete</button>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>

</main>

<script>
  document.getElementById('logoutBtn').addEventListener('click', function() {
    window.location.href = 'login.php?logout=1';
  });
</script>
</body>
</html>
