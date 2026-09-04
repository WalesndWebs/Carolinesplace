<?php
// Determine current page for active nav link
$current = basename($_SERVER['PHP_SELF'], '.php');
$root    = '/';  // change if installed in a subfolder, e.g. '/carolines/'
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Caroline\'s Place' : "Caroline's Place — A Private Sanctuary" ?></title>
  <meta name="description" content="<?= isset($pageDesc) ? htmlspecialchars($pageDesc) : "Caroline's Place — a luxury private members' club and spa in Lagos, Nigeria. Discretion, refinement, and bespoke experiences." ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= $root ?>assets/css/style.css" />
</head>
<body>

<nav class="nav" id="mainNav">
  <a href="<?= $root ?>index.php" class="nav__logo" aria-label="Caroline's Place Home">
    <img
      src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=Minimal%20flat%202D%20vector%20logo%2C%20thin%20outline%20orange%20house%20shape%20icon%20to%20the%20left%20of%20orange%20cursive%20handwritten%20script%20%22Caroline%27s%20Place%22%20text%20with%20thick%20orange%20underline%20line%20below%20all%2C%20solid%20pure%20white%20background%2C%20warm%20tangerine%20orange%20brand%20color%2C%20no%20shadows%2C%20flat%20design%2C%20clean%20minimalist%20branding&image_size=square_hd"
      alt="Caroline's Place Logo"
      class="nav__logo-img nav__logo-img--transparent"
      loading="eager"
      fetchpriority="high"
    />
  </a>

  <div class="nav__links">
    <a href="<?= $root ?>index.php"     class="nav__link <?= $current === 'index'     ? 'active' : '' ?>">Home</a>
    <a href="<?= $root ?>clubhouse.php" class="nav__link <?= $current === 'clubhouse' ? 'active' : '' ?>">Club House</a>
    <a href="<?= $root ?>spa.php"       class="nav__link <?= ($current === 'spa' || $current === 'book') ? 'active' : '' ?>">Spa</a>
    <a href="<?= $root ?>spa_menu.php"  class="btn-book">Book Experience</a>
  </div>

  <button class="nav__hamburger" id="menuToggle" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="nav__mobile" id="mobileMenu">
  <a href="<?= $root ?>index.php"     class="nav__link">Home</a>
  <a href="<?= $root ?>clubhouse.php" class="nav__link">Club House</a>
  <a href="<?= $root ?>spa.php"       class="nav__link">Spa</a>
  <a href="<?= $root ?>spa_menu.php"  class="btn-book">Book Experience</a>
</div>

<script src="<?= $root ?>assets/js/main.js" defer></script>
