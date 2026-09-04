<?php
$pageTitle = "A Private Sanctuary";
$pageDesc  = "Caroline's Place — a luxury private members' club and spa in Lagos, Nigeria.";
$root      = './';
include __DIR__ . '/includes/header.php';
?>

<!-- ================================================================
     ✨ SECTION 01 — HERO: SPLIT LAYOUT (Cream LEFT / Building RIGHT)
     Exact copy from customer's attached luxury design! 1:1 match
     ================================================================ -->
<section class="hero-split">
  <!-- LEFT: Text Column (Cream/Beige solid bg — NOT overlay!) -->
  <div class="hero-split__text">
    <p class="hero-split__eyebrow">A PRIVATE SANCTUARY</p>
    <h1 class="hero-split__title">
      Where Refined<br>
      Living Meets<br>
      Purposeful Connection.
    </h1>
    <div class="hero-split__divider">
      <span style="width:28px;background:#b8895a;"></span>
      <span style="width:14px;background:#b8895a;opacity:0.55;"></span>
    </div>
    <p class="hero-split__desc">
      Caroline's Place is more than a space — it's a community for
      women who lead, inspire and create lasting impact.
    </p>
    <a href="spa_menu.php" class="btn-book btn-book--filled">
      BOOK YOUR EXPERIENCE <span style="margin-left:10px;">→</span>
    </a>
  </div>
  <!-- RIGHT: HERO SLIDER! (3 slides: Building / Gym / Spa) with crossfade transitions, dots + arrows -->
  <div class="hero-split__img-wrap hero-slider" id="heroSlider">
    <!-- SLIDE 1: Building (active by default) -->
    <div class="hero-slider__slide hero-slider__slide--active">
      <img
        src="assets/images/hero-building.jpg"
        alt="Caroline's Place — Exterior Building"
        class="hero-slider__img"
        fetchpriority="high"
        loading="eager"
        onerror="this.onerror=null;this.src='assets/images/DSC_30541.jpg';"
      />
    </div>
    <!-- SLIDE 2: Gym / Fitness -->
    <div class="hero-slider__slide">
      <img
        src="assets/images/DSC_74331.jpg"
        alt="Caroline's Place — Gym &amp; Fitness Center"
        class="hero-slider__img"
        loading="lazy"
        onerror="this.onerror=null;this.src='assets/images/DSC_3048.jpg';"
      />
    </div>
    <!-- SLIDE 3: Spa / Nail Studio -->
    <div class="hero-slider__slide">
      <img
        src="assets/images/DSC_2976.jpg"
        alt="Caroline's Place — N Lounge &amp; Spa"
        class="hero-slider__img"
        loading="lazy"
        onerror="this.onerror=null;this.src='assets/images/DSC_2991.jpg';"
      />
    </div>

    <!-- Prev / Next Arrows -->
    <button type="button" class="hero-slider__arrow hero-slider__arrow--prev" data-hero-slider-dir="-1" aria-label="Previous slide">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button type="button" class="hero-slider__arrow hero-slider__arrow--next" data-hero-slider-dir="1" aria-label="Next slide">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>

    <!-- Dots Navigation -->
    <div class="hero-slider__dots" role="tablist" aria-label="Hero slider pages">
      <button type="button" class="hero-slider__dot hero-slider__dot--active" data-hero-slider-idx="0" role="tab" aria-label="Slide 1 — Building"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="1" role="tab" aria-label="Slide 2 — Gym"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="2" role="tab" aria-label="Slide 3 — Spa"></button>
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ SECTION 02 — 4 FEATURE ICONS (Premium / Community / Privacy / UX)
     Crown · Heart · Shield · Sparkle line icons + subtext 1:1
     ================================================================ -->
<section class="features reveal">
  <div class="features__grid">
    <!-- #1 -- Premium Spaces -->
    <div class="feature">
      <div class="feature__icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 19h20l-2-11H4L2 19z"/><path d="M12 4c-1 3-4 5-7 5 0 4 2.5 7 7 7 4.5 0 7-3 7-7-3 0-6-2-7-5z"/>
        </svg>
      </div>
      <h4 class="feature__title">Premium Spaces</h4>
      <p class="feature__text">Elegantly curated spaces for work, wellness and connection.</p>
    </div>
    <!-- #2 -- Purposeful Community -->
    <div class="feature">
      <div class="feature__icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <h4 class="feature__title">Purposeful Community</h4>
      <p class="feature__text">A network of intentional women supporting each other's growth.</p>
    </div>
    <!-- #3 -- Privacy & Comfort -->
    <div class="feature">
      <div class="feature__icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <h4 class="feature__title">Privacy &amp; Comfort</h4>
      <p class="feature__text">Discreet, secure and beautifully designed for your peace of mind.</p>
    </div>
    <!-- #4 -- Unforgettable Experiences -->
    <div class="feature">
      <div class="feature__icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2v6M12 16v6M4.93 4.93l4.24 4.24M14.83 14.83l4.24 4.24M2 12h6M16 12h6M4.93 19.07l4.24-4.24M14.83 9.17l4.24-4.24"/>
        </svg>
      </div>
      <h4 class="feature__title">Unforgettable Experiences</h4>
      <p class="feature__text">From spa days to executive retreats — every moment is elevated.</p>
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ SECTION 03 — CHOOSE YOUR EXPERIENCE: Spaces Designed For You
     2 floating rounded-corners cards + EXPLORE gold accent links 1:1
     ================================================================ -->
<section class="spaces section reveal">
  <div class="container">
    <div class="section__header">
      <p class="section__eyebrow">CHOOSE YOUR EXPERIENCE</p>
      <div class="divider divider--gold"></div>
      <h2 class="section__title">Spaces Designed For You</h2>
    </div>
    <div class="spaces__grid">
      <!-- CARD 1: The Club House -->
      <a href="clubhouse.php" class="space-card">
        <div class="space-card__img-wrap">
          <img src="assets/images/DSC_74331.jpg" alt="The Club House" loading="lazy" class="space-card__img" />
        </div>
        <div class="space-card__body">
          <h3 class="space-card__title">The Club House</h3>
          <p class="space-card__desc">
            Exclusive spaces for meetings, private gatherings, and focused productivity.
          </p>
          <span class="space-card__link">EXPLORE CLUB HOUSE <span aria-hidden="true">→</span></span>
        </div>
      </a>
      <!-- CARD 2: N Lounge & Spa -->
      <a href="spa.php" class="space-card">
        <div class="space-card__img-wrap">
          <img src="assets/images/DSC_7595.jpg" alt="N Lounge &amp; Spa" loading="lazy" class="space-card__img" />
        </div>
        <div class="space-card__body">
          <h3 class="space-card__title">N Lounge &amp; Spa</h3>
          <p class="space-card__desc">
            Relax and rejuvenate with luxury spa, beauty and wellness treatments.
          </p>
          <span class="space-card__link">EXPLORE SPA <span aria-hidden="true">→</span></span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ SECTION 04 — MORE THAN A PLACE: Sanctuary That Empowers
     LEFT = interior lounge image / RIGHT = DARK (near-black) copy column
     1:1 match! Discover Our Story outlined button
     ================================================================ -->
<section class="sanctuary reveal">
  <!-- LEFT: Image -->
  <div class="sanctuary__img-wrap">
    <img
      src="assets/images/DSC_2929.jpg"
      alt="Caroline's Place — Lounge Interior"
      loading="lazy"
      class="sanctuary__img"
      onerror="this.onerror=null;this.src='assets/images/DSC_3036.jpg';"
    />
  </div>
  <!-- RIGHT: Dark Solid Copy -->
  <div class="sanctuary__copy">
    <p class="sanctuary__eyebrow">MORE THAN A PLACE</p>
    <h2 class="sanctuary__title">A Sanctuary<br>That Empowers</h2>
    <p class="sanctuary__text">
      Every detail at Caroline's Place is thoughtfully curated to help you
      unwind, connect and thrive in an environment that feels like home.
    </p>
    <a href="clubhouse.php" class="btn btn--outline-white btn--text-left">
      DISCOVER OUR STORY <span aria-hidden="true">→</span>
    </a>
  </div>
</section>

<!-- ================================================================
     ✨ SECTION 05 — READY TO EXPERIENCE MORE? CTA CREAM
     Heading: Your Journey Starts Here
     BOOK YOUR EXPERIENCE centered button
     ================================================================ -->
<section class="journey section reveal">
  <div class="container container--sm text-center">
    <p class="section__eyebrow">READY TO EXPERIENCE MORE?</p>
    <div class="divider divider--gold"></div>
    <h2 class="section__title">Your Journey Starts Here</h2>
    <p class="journey__text">
      Book your experience today and become part of a community where
      leaders grow together.
    </p>
    <a href="spa_menu.php" class="btn-book btn-book--filled">
      BOOK YOUR EXPERIENCE <span style="margin-left:10px;" aria-hidden="true">→</span>
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
