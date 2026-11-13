(() => {
  'use strict';
  const menu = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#main-nav');
  const closeMenu = () => { nav?.classList.remove('is-open'); menu?.setAttribute('aria-expanded', 'false'); menu?.setAttribute('aria-label', 'Otvori izbornik'); };
  menu?.addEventListener('click', () => { const open = menu.getAttribute('aria-expanded') !== 'true'; nav.classList.toggle('is-open', open); menu.setAttribute('aria-expanded', String(open)); menu.setAttribute('aria-label', open ? 'Zatvori izbornik' : 'Otvori izbornik'); });
  nav?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && menu?.getAttribute('aria-expanded') === 'true') { closeMenu(); menu.focus(); } });
  document.addEventListener('click', e => { if (!e.target.closest('.site-header')) closeMenu(); });
  window.matchMedia('(min-width: 961px)').addEventListener('change', closeMenu);

  const galleryDialog = document.querySelector('#gallery-dialog');
  document.querySelectorAll('[data-gallery]').forEach(button => button.addEventListener('click', () => {
    const image = document.querySelector('#gallery-image');
    image.src = button.dataset.gallery;
    image.alt = button.dataset.caption;
    document.querySelector('#gallery-caption').textContent = button.dataset.caption;
    galleryDialog.showModal();
  }));
  document.querySelector('[data-close-gallery]')?.addEventListener('click', () => galleryDialog.close());
  galleryDialog?.addEventListener('click', event => {
    if (event.target === galleryDialog) {
      const rect = galleryDialog.getBoundingClientRect();
      if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) galleryDialog.close();
    }
  });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      for (const entry of entries) if (entry.isIntersecting) nav?.querySelectorAll('a').forEach(a => a.classList.toggle('active', a.hash === `#${entry.target.id}`));
    }, { rootMargin: '-20% 0px -60% 0px' });
    document.querySelectorAll('main section[id]').forEach(section => observer.observe(section));
  }
})();
