<?php
$pageTitle = "The Club House";
$pageDesc  = "Executive lounges, conference suites, meeting spaces, and sophisticated private-member environments at Caroline's Place Lagos.";
$root      = './';
include __DIR__ . '/includes/header.php';
?>

<!-- ================================================================
     ✨ CLUBHOUSE #1: PAGE HERO: Eyebrow + Huge Title + Full Rounded Bar Photo
     Exact 1:1 with attached clubhouse image!
     ================================================================ -->
<section class="section reveal" style="padding-top: 120px; padding-bottom: 0; background: var(--bg);">
  <div class="container" style="max-width: 1200px;">
    <p class="page-hero__eyebrow">THE PREMIUM LEADERSHIP RETREAT</p>
    <h1 class="page-hero__title">
      The Club House
    </h1>
    <div class="hero-split__divider" style="justify-content: center; display:flex; margin-bottom: 22px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <p class="page-hero__sub">
      An elegant, private space designed for executives to connect, collaborate,<br>
      and lead with clarity, purpose, and excellence.
    </p>

    <!-- HERO PHOTO: FULL Rounded corners! (Bar with brown leather chairs) -->
    <div class="club-hero-img-wrap">
      <img
        src="assets/images/DSC_4045.jpg"
        alt="The Club House — Bar &amp; Lounge"
        class="club-hero-img"
        loading="eager"
        fetchpriority="high"
        onerror="this.onerror=null;this.src='assets/images/DSC_3010.jpg';"
      />
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ CLUBHOUSE #2: SPLIT Boardroom image LEFT + "Where Intimate Meets Luxury" copy RIGHT
     ================================================================ -->
<section class="page-split reveal">
  <div class="page-split__img">
    <img src="assets/images/DSC_7433.jpg" alt="Executive Boardroom — Conference Room" loading="lazy" class="page-split__rounded-img" />
  </div>
  <div class="page-split__copy">
    <p class="page-split__eyebrow">PRIVATE SPACES</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title">Where Intimate<br>Meets Luxury</h2>
    <p class="page-split__text">
      Our Club House offers a refined environment for meaningful conversations,
      focused collaboration, and exceptional experiences.<br>
      Designed for leaders, built for legacy.
    </p>
  </div>
</section>

<!-- ================================================================
     ✨ CLUBHOUSE #3: SPLIT DARK — "Executive Lounge" copy LEFT (NEAR BLACK BG) + LONG Lounge photo RIGHT
     ================================================================ -->
<section class="page-split page-split--dark page-split--reverse reveal">
  <div class="page-split__copy page-split__copy--dark" style="padding: 96px 6vw;">
    <p class="page-split__eyebrow page-split__eyebrow--light">EXCLUSIVE ACCESS</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title page-split__title--light">The Executive<br>Lounge</h2>
    <p class="page-split__eyebrow page-split__eyebrow--light" style="margin-top: 4px; margin-bottom: 16px; letter-spacing: 0.16em; color: rgba(245,236,221,0.65); font-size: 0.68rem; font-weight: 500; text-transform: uppercase;">Unwind · Recharge · Connect.</p>
    <p class="page-split__text page-split__text--light">
      The Executive Lounge is your private escape within the Club House. Enjoy
      curated comfort, quiet moments, and purposeful connections in a space
      designed to elevate every conversation.
    </p>
  </div>
  <div class="page-split__img page-split__img--tall">
    <img src="assets/images/DSC_3010.jpg" alt="Executive Lounge — Long Interior" loading="lazy" class="page-split__img-dark" />
  </div>
</section>

<!-- ================================================================
     ✨ CLUBHOUSE #4: FACILITIES 4 × 2 IMAGE GRID (8 facilities!)
     1:1 match attached clubhouse facilities grid!
     ================================================================ -->
<section class="section reveal" style="background: linear-gradient(180deg, #ffffff 0%, #FAF3E7 100%); padding-top: 72px; padding-bottom: 80px;">
  <div class="container" style="max-width: 1280px;">
    <p class="section__eyebrow text-center" style="display:block; text-align: center;">OUR CLUB HOUSE</p>
    <div class="divider divider--gold" style="margin: 14px auto 24px;"></div>
    <h2 class="section__title text-center" style="text-align:center; margin-bottom: 56px;">Our Club House Facilities</h2>

    <div class="facilities-grid">
      <!-- 1 Executive Lounge -->
      <div class="facility-tile">
        <img src="assets/images/DSC_4045.jpg" alt="Executive Lounge" loading="lazy" class="facility-tile__img" />
        <span class="facility-tile__label">EXECUTIVE LOUNGE</span>
      </div>
      <!-- 2 Wellness Studio -->
      <div class="facility-tile">
        <img src="assets/images/DSC_75020.jpg" alt="Wellness Studio" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_7476.jpg';" />
        <span class="facility-tile__label">WELLNESS STUDIO</span>
      </div>
      <!-- 3 Conference Room -->
      <div class="facility-tile">
        <img src="assets/images/DSC_7433.jpg" alt="Conference Room" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_74331.jpg';" />
        <span class="facility-tile__label">CONFERENCE ROOM</span>
      </div>
      <!-- 4 Private Dining -->
      <div class="facility-tile">
        <img src="assets/images/DSC_7476.jpg" alt="Private Dining" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_2929.jpg';" />
        <span class="facility-tile__label">PRIVATE DINING</span>
      </div>
      <!-- 5 Member's Lounge -->
      <div class="facility-tile">
        <img src="assets/images/DSC_2929.jpg" alt="Member's Lounge" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_7502.jpg';" />
        <span class="facility-tile__label">MEMBER'S LOUNGE</span>
      </div>
      <!-- 6 Fitness Center -->
      <div class="facility-tile">
        <img src="assets/images/DSC_74331.jpg" alt="Fitness Center" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_3048.jpg';" />
        <span class="facility-tile__label">FITNESS CENTER</span>
      </div>
      <!-- 7 Game Room -->
      <div class="facility-tile">
        <img src="assets/images/DSC_3036.jpg" alt="Game Room" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_3030.jpg';" />
        <span class="facility-tile__label">GAME ROOM</span>
      </div>
      <!-- 8 Outdoor Terrace -->
      <div class="facility-tile">
        <img src="assets/images/DSC_7463.jpg" alt="Outdoor Terrace" loading="lazy" class="facility-tile__img" onerror="this.onerror=null;this.src='assets/images/DSC_2929.jpg';" />
        <span class="facility-tile__label">OUTDOOR TERRACE</span>
      </div>
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ CLUBHOUSE #5: FINAL CTA ORNATE GOLD CARD (1:1!)
     "Ready to Experience Clubhouse?" JOIN THE CLUBHOUSE BUTTON
     ================================================================ -->
<section class="section reveal" style="background: linear-gradient(180deg, #FAF3E7 0%, #F4E9D5 100%); padding-top: 60px; padding-bottom: 96px;">
  <div class="container container--md" style="max-width: 820px;">
    <div class="cta-ornate-card">
      <div class="cta-ornate-card__sparkle cta-ornate-card__sparkle--tl" aria-hidden="true">✦</div>
      <div class="cta-ornate-card__sparkle cta-ornate-card__sparkle--tr" aria-hidden="true">✦</div>
      <div class="hero-split__divider" style="justify-content: center; display:flex; margin-bottom: 16px;">
        <span style="width:40px;background:#b8895a;"></span>
      </div>
      <h2 class="section__title" style="margin-bottom: 20px;">Ready to Experience The<br>Clubhouse?</h2>

      <p class="cta-ornate-card__lead">
        Complete your membership payment and begin your journey toward leadership excellence.
      </p>

      <div style="margin: 40px 0 20px; display: flex; justify-content: center;">
        <a
          href="https://bolamatelokoh.com/carolinesplace/"
          target="_blank"
          rel="noopener noreferrer"
          class="btn-book btn-book--filled"
          style="align-self:center; padding: 14px 32px;"
        >
          JOIN THE CLUBHOUSE
        </a>
      </div>

      <p class="cta-ornate-card__footnote" style="letter-spacing: 0.18em;">
        CAROLINE'S PLACE &nbsp;·&nbsp; WHERE LEADERS GROW
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
