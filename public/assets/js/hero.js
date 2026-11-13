(() => {
  'use strict';
  const hero = document.querySelector('[data-hero-slider]');
  if (!hero) return;
  const slides = [...hero.querySelectorAll('[data-hero-slide]')];
  const dots = [...hero.querySelectorAll('[data-hero-dot]')];
  const controls = hero.querySelector('.hero-controls');
  const toggle = hero.querySelector('[data-hero-toggle]');
  if (slides.length < 2 || !controls || !toggle) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const failed = new Set();
  let current = 0;
  let paused = reducedMotion.matches;
  let focused = false;
  let inView = true;
  let timer;
  let request = 0;

  const canRotate = () => !paused && !focused && inView && !document.hidden && slides.length - failed.size > 1;
  const schedule = () => {
    window.clearTimeout(timer);
    const label = paused ? toggle.dataset.playLabel : toggle.dataset.pauseLabel;
    toggle.setAttribute('aria-label', label);
    toggle.title = label;
    toggle.querySelector('[data-hero-pause-icon]').hidden = paused;
    toggle.querySelector('[data-hero-play-icon]').hidden = !paused;
    if (!canRotate()) return;
    timer = window.setTimeout(() => {
      let next = (current + 1) % slides.length;
      while (failed.has(next)) next = (next + 1) % slides.length;
      show(next);
    }, 6000);
  };

  const show = async (index, manual = false) => {
    const pending = ++request;
    window.clearTimeout(timer);
    const image = slides[index].querySelector('img');
    try {
      // Keep the current photo visible until the incoming image is ready.
      await image.decode();
    } catch {
      if (pending !== request) return;
      failed.add(index);
      dots[index].disabled = true;
      schedule();
      return;
    }
    if (pending !== request) return;
    if (!manual && !canRotate()) { schedule(); return; }
    current = index;
    slides.forEach((slide, position) => {
      slide.classList.toggle('is-active', position === current);
      slide.setAttribute('aria-hidden', String(position !== current));
      dots[position].setAttribute('aria-pressed', String(position === current));
    });
    schedule();
  };

  dots.forEach((dot, index) => dot.addEventListener('click', () => {
    paused = true;
    schedule();
    show(index, true);
  }));
  toggle.addEventListener('click', () => {
    paused = !paused;
    if (!paused) focused = false;
    ++request;
    schedule();
  });
  hero.addEventListener('focusin', event => { focused = event.target.matches(':focus-visible'); schedule(); });
  hero.addEventListener('focusout', event => { focused = hero.contains(event.relatedTarget) && event.relatedTarget.matches(':focus-visible'); schedule(); });
  document.addEventListener('visibilitychange', schedule);
  reducedMotion.addEventListener('change', () => {
    if (reducedMotion.matches) paused = true;
    ++request;
    schedule();
  });
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(entries => {
      inView = entries[0].isIntersecting;
      schedule();
    }, { threshold: 0 }).observe(hero);
  }
  controls.hidden = false;
  schedule();
})();
