<?php
/**
 * Caroline's Place — Global Header
 */
$pageTitle = $pageTitle ?? "Caroline's Place — A Private Sanctuary";
$pageDesc  = $pageDesc  ?? "Caroline's Place — a luxury private members' club and spa in Lagos, Nigeria. Discretion, refinement, and bespoke experiences.";
$current   = $current   ?? 'index';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/png" href="/assets/images/favicon.png?v=<?= time() ?>" />
  <link rel="apple-touch-icon" href="/assets/images/favicon.png?v=<?= time() ?>" />
  <link rel="stylesheet" href="/assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

<nav class="nav <?php echo $current === 'spa' ? 'nav--spa' : ''; ?>" id="mainNav">
  <a href="/" class="nav__logo" aria-label="The Club House @ Caroline's Place">
    <img
      src="/assets/images/dsclogo.png?v=<?= time() ?>"
      alt="The Club House @ Caroline's Place"
      class="nav__logo-img nav__logo-img--transparent"
      loading="eager"
      fetchpriority="high"
    />
    <?php if ($current === 'spa'): ?>
      <span class="nav__logo-sep" aria-hidden="true" style="display:inline-block; width:1px; height:24px; background:rgba(215,181,118,0.45); margin:0 10px;"></span>
      <div class="nav__spa-brand-blend" title="The Nail Lounge &amp; Spa at Caroline's Place">
        <img
          src="/assets/images/nlogo.png?v=<?= time() ?>"
          alt="The Nail Lounge &amp; Spa Emblem"
          class="nav__spa-logo-img"
          loading="eager"
        />
        <div class="nav__spa-brand-text">
          <span class="nav__spa-brand-title">THE NAIL LOUNGE</span>
          <span class="nav__spa-brand-sub">&amp; SPA</span>
        </div>
      </div>
    <?php endif; ?>
  </a>

  <div class="nav__links">
    <a href="/" class="nav__link <?php echo $current === 'index' ? 'active' : ''; ?>">Home</a>
    <a href="/clubhouse.php" class="nav__link <?php echo $current === 'clubhouse' ? 'active' : ''; ?>">Club House</a>
    <a href="/spa.php" class="nav__link <?php echo in_array($current, ['spa', 'book']) ? 'active' : ''; ?>">Spa</a>
    <a href="/spa_menu.php" class="btn-book btn-book--nav">Book Experience</a>
  </div>

  <button class="nav__hamburger" id="menuToggle" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="nav__mobile" id="mobileMenu">
  <div class="nav__mobile-brand" style="padding: 18px 24px 12px; border-bottom: 1px solid rgba(27,20,16,0.08); margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
    <img src="/assets/images/dsclogo.png?v=<?= time() ?>" alt="The Club House @ Caroline's Place" style="height: 38px; width: auto; object-fit: contain; display: block;" />
    <?php if ($current === 'spa'): ?>
      <span style="display: inline-block; width: 1px; height: 26px; background: rgba(215,181,118,0.5);"></span>
      <img src="/assets/images/nlogo.png?v=<?= time() ?>" alt="The Nail Lounge &amp; Spa" style="height: 38px; width: 38px; border-radius: 50%; object-fit: contain; display: block;" />
    <?php endif; ?>
  </div>
  <a href="/" class="nav__link">Home</a>
  <a href="/clubhouse.php" class="nav__link">Club House</a>
  <a href="/spa.php" class="nav__link">Spa</a>
  <a href="/spa_menu.php" class="btn-book">Book Experience</a>
</div>

<script src="/assets/js/main.js" defer></script>
