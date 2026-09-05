<?php
/**
 * Caroline's Place — N Lounge & Spa
 */
$pageTitle = "N Lounge & Spa — Caroline's Place";
$pageDesc  = "Your sanctuary of beauty, relaxation and pampering — luxury hair care, nail studio, massage and body treatments in Lagos.";
$current   = 'spa';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ================================================================
     ✨ SPA HERO SPLIT: LEFT text + RIGHT nail salon image
     ================================================================ -->
<section class="hero-split hero-split--page">
  <div class="hero-split__text">
    <div class="spa-page__brand-lockup">
      <div class="spa-page__brand-logo-frame">
        <img
          src="/assets/images/logo.jpg"
          alt="The Nail Lounge &amp; Spa Logo"
          class="spa-page__brand-logo"
          width="114"
          height="114"
          fetchpriority="high"
          loading="eager"
        />
      </div>
      <div class="spa-page__brand-meta">
        <span class="spa-page__brand-badge">Bespoke Wellness Sanctuary</span>
        <span class="spa-page__brand-sub">The Nail Lounge &amp; Spa · Lagos</span>
      </div>
    </div>
    <p class="hero-split__eyebrow">WELLNESS &amp; BEAUTY</p>
    <h1 class="hero-split__title" style="font-size: clamp(2.6rem, 5vw, 4.5rem); letter-spacing: -0.01em;">
      The Nail Lounge<br>&amp; Spa
    </h1>
    <div class="hero-split__divider">
      <span style="width:28px;background:#b8895a;"></span>
      <span style="width:14px;background:#b8895a;opacity:0.55;"></span>
    </div>
    <p class="hero-split__desc">
      Your sanctuary of beauty, relaxation and pampering — where luxury hair care,
      bespoke nail studio artistry, therapeutic massage and restorative body rituals are curated exclusively for you.
    </p>
  </div>
  <!-- RIGHT: 5-ATTACHED-IMAGE HERO SLIDER! ONLY the 5 images you sent! -->
  <div class="hero-split__img-wrap hero-slider hero-slider--spa" id="spaHeroSlider">
    <!-- SLIDE 1: Hot Stone Massage (1st attached image) -->
    <div class="hero-slider__slide hero-slider__slide--active">
      <img
        src="assets/images/spa2.jpg" alt="Hot Stone Massage — N Lounge &amp; Spa"
        class="hero-slider__img"
        fetchpriority="high"
        loading="eager"
      />
    </div>
    <!-- SLIDE 2: Blue Royal Manicure + Tayo caption (2nd attached image) -->
    <div class="hero-slider__slide">
      <img
        src="assets/images/nail2.jpg"
        alt="Royal Blue Gel Manicure — Ask for Tayo"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>
    <!-- SLIDE 3: Knotless Box Braids (3rd attached image) -->
    <div class="hero-slider__slide">
      <img
        src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=Closeup%20overhead%20back%20view%20of%20woman%20newly%20done%20hairstyle%2C%20neat%20thick%20sleek%20black%20knotless%20box%20braids%20feed%20in%20cornrows%20with%20perfect%20straight%20crisp%20scalp%20part%20lines%20exposing%20warm%20honey%20blonde%20scalp%20skin%2C%20glossy%20shiny%20extension%20hair%20texture%2C%20dark%20salon%20station%20table%20in%20background%2C%20real%20hairdresser%20portfolio%20photography&image_size=landscape_16_9"
        alt="Knotless Box Braids — Hair Studio"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>
    <!-- SLIDE 4: Foot Reflexology Massage (4th attached image) -->
    <div class="hero-slider__slide">
      <img
        src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=Wide%20panoramic%20photo%20of%20African%20melanin%20woman%20bare%20feet%20on%20spa%20table%2C%20professional%20therapist%20hands%20massaging%20foot%20sole%20reflexology%2C%20therapist%20wearing%20clean%20white%20uniform%2C%20soft%20fluffy%20rolled%20white%20towel%20under%20calves%2C%20bright%20airy%20white%20treatment%20room%20with%20blurred%20green%20potted%20plants%20in%20background%2C%20soft%20dreamy%20bokeh%20edges%2C%20real%20wellness%20photography&image_size=landscape_16_9"
        alt="Foot Reflexology &amp; Massage"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>
    <!-- SLIDE 5: V-TEN Treatment / Facial Room (5th attached image) -->
    <div class="hero-slider__slide">
      <img
        src="assets/images/DSC_75020.jpg"
        alt="V-TEN Advanced Treatment Room"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>
     <!-- SLIDE 5: V-TEN Treatment / Facial Room (5th attached image) -->
    <div class="hero-slider__slide">
      <img
        src="assets/images/DSC_7476.jpg"
        alt="V-TEN Advanced Treatment Room"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>
    <div class="hero-slider__slide">
      <img
        src="assets/images/slid1.jpg"
        alt="V-TEN Advanced Treatment Room"
        class="hero-slider__img"
        loading="lazy"
      />
    </div>


    <!-- Prev / Next Arrows -->
    <button type="button" class="hero-slider__arrow hero-slider__arrow--prev" data-hero-slider-dir="-1" aria-label="Previous slide">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button type="button" class="hero-slider__arrow hero-slider__arrow--next" data-hero-slider-dir="1" aria-label="Next slide">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>

    <!-- Dots Navigation (5 dots!) -->
    <div class="hero-slider__dots" role="tablist" aria-label="Spa hero slider pages">
      <button type="button" class="hero-slider__dot hero-slider__dot--active" data-hero-slider-idx="0" role="tab" aria-label="Slide 1 — Hot Stone Massage"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="1" role="tab" aria-label="Slide 2 — Blue Gel Manicure"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="2" role="tab" aria-label="Slide 3 — Knotless Braids"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="3" role="tab" aria-label="Slide 4 — Foot Reflexology"></button>
      <button type="button" class="hero-slider__dot" data-hero-slider-idx="4" role="tab" aria-label="Slide 5 — V-TEN Room"></button>
    </div>
  </div>
</section>

<!-- ================================================================
     ✨ SPA SPLIT 1: LEFT image hair studio + RIGHT Expert Hands copy
     ================================================================ -->
<section class="page-split reveal">
  <div class="page-split__img">
    <img src="/assets/images/dsc1.jpg" alt="Hair &amp; Nail Studio" loading="lazy" class="page-split__rounded-img" />
  </div>
  <div class="page-split__copy">
    <p class="page-split__eyebrow">HAIR &amp; NAIL STUDIO</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title">Expert Hands,<br>Uncompromising Quality</h2>
    <p class="page-split__text">
      From signature blow-outs, Ghana weaving, knotless braids, wig
      construction &amp; installations to luxury gel manicures &amp; pedicures.
      Our hair stylists and nail technicians are trained to deliver a
      flawless finish that lasts — every single visit.
    </p>
  </div>
</section>

<!-- ================================================================
     ✨ NEW! MANICURE SECTION: LEFT copy + RIGHT Blue Manicure image
     ================================================================ -->
<section class="page-split page-split--reverse reveal">
  <div class="page-split__copy">
    <p class="page-split__eyebrow">MANICURE STUDIO</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title">Flawless Manicures,<br>Handcrafted For You.</h2>
    <p class="page-split__text" style="margin-bottom: 22px;">
      From everyday elegance to show-stopping statement nails — every manicure is shaped,
      buffed and polished by our certified artists for a chip-resistant, high-shine finish
      that lasts weeks, not days.
    
      — our senior nail technician and the fan-favourite behind the flawless royal blue sets everyone is booking.&nbsp;✨
    </p>
  </div>
  <div class="page-split__img">
    <img
      src="assets/images/nail.jpg"
      alt="Royal Blue Gel Manicure — Ask for Tayo"
      loading="lazy"
      class="page-split__rounded-img page-split__rounded-img--glow"
    />
  </div>
</section>

<!-- ================================================================
     ✨ SPA SPLIT 3: LEFT Pedi copy + RIGHT nail stations image
     ================================================================ -->
<section class="page-split reveal">
  <div class="page-split__copy">
    <p class="page-split__eyebrow">HAIR &amp; NAIL STUDIO</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title">Pedicure &amp;<br>Foot Care,</h2>
    <p class="page-split__text">
      Treat your feet to expert care with our signature pedicures,
      soothing treatments and refined finishing. Step out feeling refreshed,
      polished and perfectly cared for — in the hands of our skilled beauty therapists.
    </p>
  </div>
  <div class="page-split__img">
    <img src="/assets/images/DSC_7496.jpg" alt="Pedicure &amp; Nail Care Stations" loading="lazy" class="page-split__rounded-img" />
  </div>
</section>

<!-- ================================================================
     ✨ SPA SPLIT 3: LEFT massage beds image + RIGHT DARK copy column
     ================================================================ -->
<section class="page-split page-split--dark reveal">
  <div class="page-split__img page-split__img--tall">
    <img src="/assets/images/DSC_7595.jpg" alt="Massage &amp; Treatment Suite" loading="lazy" class="page-split__img-dark" />
  </div>
  <div class="page-split__copy page-split__copy--dark">
    <p class="page-split__eyebrow page-split__eyebrow--light">MASSAGE, FACIALS &amp; BODY</p>
    <div class="hero-split__divider" style="margin-bottom: 20px;">
      <span style="width:28px;background:#b8895a;"></span>
    </div>
    <h2 class="page-split__title page-split__title--light">Body Treatments<br>&amp; Deep Relaxation</h2>
    <p class="page-split__text page-split__text--light">
      Unwind with our signature facials, therapeutic massage, full body
      treatments and waxing services. Step out feeling renewed, restored and
      glowing — in the hands of our certified beauty therapists.
    </p>
  </div>
</section>

<!-- ================================================================
     ✨ SPA FINAL CTA: DOTTED GOLD CARD + VIEW MENU BUTTON
     ================================================================ -->
<section class="section reveal" style="background: linear-gradient(180deg, #FAF3E7 0%, #F4E9D5 100%); padding-top: 80px; padding-bottom: 100px;">
  <div class="container container--md" style="max-width: 820px;">
    <div class="cta-ornate-card">
      <div class="cta-ornate-card__chevron" aria-hidden="true">
        <svg width="28" height="14" viewBox="0 0 28 14" fill="none" stroke="#b8895a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 2 L14 12 L26 2"/></svg>
      </div>
      <div class="cta-ornate-card__sparkle cta-ornate-card__sparkle--tl" aria-hidden="true">✦</div>
      <div class="cta-ornate-card__sparkle cta-ornate-card__sparkle--tr" aria-hidden="true">✦</div>

      <p class="cta-ornate-card__eyebrow">RESERVATIONS · BOOK AHEAD</p>
      <h2 class="section__title" style="margin-bottom: 20px;">Ready to Book Your Spa<br>Experience?</h2>

      <p class="cta-ornate-card__lead">
        View our full service menu, build your perfect session — with live pricing and total before you pay.
      </p>
      <p class="cta-ornate-card__sub">
        Choose from 100+ services across hair, nails, massage, facials and body care.<br>
        Pay securely and see your grand total live as you pick.
      </p>

      <div style="margin: 36px 0 20px; display: flex; justify-content: center;">
        <a href="/spa_menu.php" class="btn-book btn-book--filled" style="align-self:center; padding: 14px 30px;">
          VIEW MENU &amp; BOOK NOW <span aria-hidden="true" style="margin-left: 10px;">→</span>
        </a>
      </div>

      <p class="cta-ornate-card__footnote">
        WALK-INS WELCOME &nbsp;·&nbsp; BOOKING RECOMMENDED FOR WEEKENDS
      </p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
