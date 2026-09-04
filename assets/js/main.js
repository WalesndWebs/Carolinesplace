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
// 🌅 HOMEPAGE HERO SLIDER (Building / Gym / Spa crossfade)
// ============================================================
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

  // Dots click
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      const i = Number(dot.dataset.heroSliderIdx ?? 0);
      goTo(i); start();
    });
  });

  // Arrows click
  arrows.forEach(btn => {
    btn.addEventListener('click', () => {
      const dir = Number(btn.dataset.heroSliderDir ?? 1);
      goTo(currentIdx + dir); start();
    });
  });

  // Pause on hover, resume on leave
  slider.addEventListener('mouseenter', stop, { passive: true });
  slider.addEventListener('mouseleave', start, { passive: true });

  // Pause when tab is hidden
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop(); else start();
  });

  start();
}

document.addEventListener('DOMContentLoaded', initHeroSlider);
