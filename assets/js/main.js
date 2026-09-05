// ============================================================
// CAROLINE'S PLACE — Shared JavaScript
// ============================================================

// ── Nav scroll effect ────────────────────────────────────────
const nav = document.getElementById('mainNav');
if (nav) {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 40);
  }, { passive: true });
}

// ── Mobile menu toggle ────────────────────────────────────────
const menuToggle  = document.getElementById('menuToggle');
const mobileMenu  = document.getElementById('mobileMenu');
if (menuToggle && mobileMenu) {
  menuToggle.addEventListener('click', () => {
    const open = mobileMenu.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', open);
    // animate hamburger → X
    const bars = menuToggle.querySelectorAll('span');
    if (open) {
      bars[0].style.transform = 'rotate(45deg) translate(4px, 4px)';
      bars[1].style.opacity   = '0';
      bars[2].style.transform = 'rotate(-45deg) translate(4px, -4px)';
    } else {
      bars[0].style.transform = '';
      bars[1].style.opacity   = '';
      bars[2].style.transform = '';
    }
  });

  // close when a link is clicked
  mobileMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => mobileMenu.classList.remove('open'));
  });
}

// ── Scroll reveal ─────────────────────────────────────────────
function initReveal() {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

  els.forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', initReveal);

// ============================================================
// 🌅 LUXURY HERO STORY SLIDER (Visual storytelling chapters)
// 6 chapters with Instagram/editorial progress bars, pause/play,
// touch swipe, hold-to-pause, and interactive chapter navigation.
// ============================================================
function initHeroStorySlider() {
  const slider = document.getElementById('heroStorySlider');
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll('.hero-story__slide'));
  const progressFills = Array.from(slider.querySelectorAll('.hero-story__progress-fill'));
  const progressSegs = Array.from(slider.querySelectorAll('.hero-story__progress-seg'));
  const counter = document.getElementById('storyCounter');
  const playToggle = document.getElementById('storyPlayToggle');
  const iconPause = playToggle ? playToggle.querySelector('.icon-pause') : null;
  const iconPlay = playToggle ? playToggle.querySelector('.icon-play') : null;
  const prevBtn = document.getElementById('storyPrevBtn');
  const nextBtn = document.getElementById('storyNextBtn');
  const tapPrev = document.getElementById('storyTapPrev');
  const tapNext = document.getElementById('storyTapNext');
  const chips = Array.from(document.querySelectorAll('.hero-chip'));

  const total = slides.length;
  if (!total) return;

  const SLIDE_DURATION = 5500; // ms per slide
  let currentIdx = 0;
  let isUserPaused = false;
  let isHolding = false;
  let isHovered = false;
  let lastTimestamp = null;
  let elapsed = 0;
  let animFrameId = null;

  // Touch swipe support
  let touchStartX = 0;
  let touchStartY = 0;

  function updateUi() {
    // Update active slide
    slides.forEach((slide, i) => {
      const isActive = (i === currentIdx);
      slide.classList.toggle('hero-story__slide--active', isActive);
      // reset kenburns by toggling active class
      const img = slide.querySelector('.hero-story__img');
      if (img && isActive) {
        img.style.animation = 'none';
        void img.offsetWidth; // trigger reflow
        img.style.animation = '';
      }
    });

    // Update progress bars
    progressFills.forEach((fill, i) => {
      if (i < currentIdx) {
        fill.style.width = '100%';
      } else if (i > currentIdx) {
        fill.style.width = '0%';
      } else {
        fill.style.width = '0%';
      }
    });

    // Update counter
    if (counter) {
      counter.textContent = `${String(currentIdx + 1).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
    }

    // Update chapter chips
    chips.forEach(chip => {
      const targetIdx = Number(chip.dataset.storyTarget);
      chip.classList.toggle('hero-chip--active', targetIdx === currentIdx);
    });
  }

  function goTo(idx) {
    const next = ((idx % total) + total) % total;
    currentIdx = next;
    elapsed = 0;
    lastTimestamp = null;
    updateUi();
  }

  function setPausedState(paused) {
    isUserPaused = paused;
    if (iconPause && iconPlay) {
      iconPause.style.display = isUserPaused ? 'none' : 'block';
      iconPlay.style.display = isUserPaused ? 'block' : 'none';
    }
    if (playToggle) {
      playToggle.setAttribute('aria-label', isUserPaused ? 'Resume story' : 'Pause story');
    }
  }

  function loop(timestamp) {
    if (!lastTimestamp) lastTimestamp = timestamp;
    const delta = timestamp - lastTimestamp;
    lastTimestamp = timestamp;

    const effectivelyPaused = isUserPaused || isHolding || isHovered || document.hidden;

    if (!effectivelyPaused) {
      elapsed += delta;
      const progress = Math.min(1, elapsed / SLIDE_DURATION);
      if (progressFills[currentIdx]) {
        progressFills[currentIdx].style.width = `${progress * 100}%`;
      }

      if (elapsed >= SLIDE_DURATION) {
        goTo(currentIdx + 1);
      }
    }

    animFrameId = requestAnimationFrame(loop);
  }

  // Prev / Next button clicks
  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      goTo(currentIdx - 1);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      goTo(currentIdx + 1);
    });
  }

  // Tap zones
  if (tapPrev) {
    tapPrev.addEventListener('click', (e) => {
      e.stopPropagation();
      goTo(currentIdx - 1);
    });
  }
  if (tapNext) {
    tapNext.addEventListener('click', (e) => {
      e.stopPropagation();
      goTo(currentIdx + 1);
    });
  }

  // Play / Pause toggle
  if (playToggle) {
    playToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      setPausedState(!isUserPaused);
    });
  }

  // Progress segments click
  progressSegs.forEach((seg, i) => {
    seg.addEventListener('click', (e) => {
      e.stopPropagation();
      goTo(i);
    });
  });

  // Chapter chips click
  chips.forEach(chip => {
    chip.addEventListener('click', (e) => {
      e.preventDefault();
      const targetIdx = Number(chip.dataset.storyTarget);
      if (!isNaN(targetIdx)) {
        goTo(targetIdx);
      }
    });
  });

  // Hold to pause (pointer events)
  slider.addEventListener('pointerdown', (e) => {
    // don't pause if clicking buttons or links
    if (e.target.closest('button, a, input, select')) return;
    isHolding = true;
  });
  window.addEventListener('pointerup', () => { isHolding = false; });
  window.addEventListener('pointercancel', () => { isHolding = false; });

  // Hover pause on desktop (soft pause when reading the card)
  slider.addEventListener('mouseenter', () => { isHovered = true; }, { passive: true });
  slider.addEventListener('mouseleave', () => { isHovered = false; isHolding = false; }, { passive: true });

  // Touch gestures (swipe left/right)
  slider.addEventListener('touchstart', (e) => {
    if (e.touches.length === 1) {
      touchStartX = e.touches[0].clientX;
      touchStartY = e.touches[0].clientY;
    }
  }, { passive: true });

  slider.addEventListener('touchend', (e) => {
    if (e.changedTouches.length === 1) {
      const diffX = e.changedTouches[0].clientX - touchStartX;
      const diffY = e.changedTouches[0].clientY - touchStartY;
      // horizontal swipe with small vertical deviation
      if (Math.abs(diffX) > 45 && Math.abs(diffY) < 65) {
        if (diffX < 0) {
          goTo(currentIdx + 1); // swipe left → next
        } else {
          goTo(currentIdx - 1); // swipe right → prev
        }
      }
    }
  }, { passive: true });

  // Keyboard navigation
  slider.setAttribute('tabindex', '0');
  slider.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') {
      e.preventDefault();
      goTo(currentIdx + 1);
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      goTo(currentIdx - 1);
    } else if (e.key === ' ') {
      e.preventDefault();
      setPausedState(!isUserPaused);
    }
  });

  // Tab visibility
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      lastTimestamp = null;
    }
  });

  // Preload images
  slides.forEach(slide => {
    const img = slide.querySelector('img');
    if (img && img.src) {
      const pre = new Image();
      pre.src = img.src;
    }
  });

  // Start story loop
  updateUi();
  animFrameId = requestAnimationFrame(loop);
}

// Fallback legacy hero slider
function initHeroSlider() {
  const slider = document.getElementById('heroSlider');
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll('.hero-slider__slide'));
  const dots   = Array.from(slider.querySelectorAll('.hero-slider__dot'));
  const arrows = Array.from(slider.querySelectorAll('.hero-slider__arrow'));
  if (!slides.length) return;

  let currentIdx = slides.findIndex(s => s.classList.contains('hero-slider__slide--active'));
  if (currentIdx < 0) currentIdx = 0;

  const INTERVAL_MS = 5000;
  let timer = null;

  function goTo(idx) {
    const n = slides.length;
    const next = ((idx % n) + n) % n;
    if (next === currentIdx) return;

    slides[currentIdx].classList.remove('hero-slider__slide--active');
    slides[next].classList.add('hero-slider__slide--active');

    dots[currentIdx]?.classList.remove('hero-slider__dot--active');
    dots[next]?.classList.add('hero-slider__dot--active');

    currentIdx = next;
  }

  function start() { stop(); timer = setInterval(() => goTo(currentIdx + 1), INTERVAL_MS); }
  function stop()  { if (timer) { clearInterval(timer); timer = null; } }

  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      const i = Number(dot.dataset.heroSliderIdx ?? 0);
      goTo(i); start();
    });
  });

  arrows.forEach(btn => {
    btn.addEventListener('click', () => {
      const dir = Number(btn.dataset.heroSliderDir ?? 1);
      goTo(currentIdx + dir); start();
    });
  });

  start();
}

document.addEventListener('DOMContentLoaded', () => {
  initHeroStorySlider();
  initHeroSlider();
  initSpaHeroSlider();
});

/* ====================================================================
   🌅 SPA.PHP HERO SLIDER (5 slides attached images! ID = spaHeroSlider)
   ==================================================================== */
function initSpaHeroSlider() {
  const slider = document.getElementById('spaHeroSlider');
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll('.hero-slider__slide'));
  const dots   = Array.from(slider.querySelectorAll('.hero-slider__dot'));
  const arrows = Array.from(slider.querySelectorAll('.hero-slider__arrow'));
  if (!slides.length) return;

  let currentIdx = slides.findIndex(s => s.classList.contains('hero-slider__slide--active'));
  if (currentIdx < 0) currentIdx = 0;

  const INTERVAL_MS = 5000;
  let timer = null;

  function goTo(idx) {
    const n = slides.length;
    const next = ((idx % n) + n) % n;
    if (next === currentIdx) return;

    slides[currentIdx].classList.remove('hero-slider__slide--active');
    slides[next].classList.add('hero-slider__slide--active');

    dots[currentIdx]?.classList.remove('hero-slider__dot--active');
    dots[next]?.classList.add('hero-slider__dot--active');

    currentIdx = next;
  }

  function start() { stop(); timer = setInterval(() => goTo(currentIdx + 1), INTERVAL_MS); }
  function stop()  { if (timer) { clearInterval(timer); timer = null; } }

  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      const i = Number(dot.dataset.heroSliderIdx ?? 0);
      goTo(i); start();
    });
  });

  arrows.forEach(btn => {
    btn.addEventListener('click', () => {
      const dir = Number(btn.dataset.heroSliderDir ?? 1);
      goTo(currentIdx + dir); start();
    });
  });

  // Pause on hover
  slider.addEventListener('mouseenter', stop, { passive: true });
  slider.addEventListener('mouseleave', start, { passive: true });
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop(); else start();
  });

  start();
}
