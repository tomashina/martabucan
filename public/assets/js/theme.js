(() => {
  'use strict';

  const storageKey = 'mb_color_theme';
  const themes = new Map([
    ['olive', '#636B2F'],
    ['cherry', '#762E40'],
    ['gold', '#B59960'],
  ]);
  let choices = [];

  const readTheme = () => {
    try { return localStorage.getItem(storageKey); }
    catch { return null; }
  };

  const applyTheme = value => {
    const theme = themes.has(value) ? value : 'olive';
    document.documentElement.dataset.theme = theme;
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', themes.get(theme));
    document.querySelector('link[rel="icon"]')?.setAttribute('href', `/favicon-${theme}.png`);
    choices.forEach(button => {
      button.setAttribute('aria-pressed', String(button.dataset.themeOption === theme));
    });
  };

  // Run in the head before styles paint, including when arriving from another page.
  applyTheme(readTheme());

  document.addEventListener('DOMContentLoaded', () => {
    const switcher = document.querySelector('[data-theme-switcher]');
    if (!switcher) return;
    choices = [...switcher.querySelectorAll('[data-theme-option]')];
    choices.forEach(button => button.addEventListener('click', () => {
      const theme = button.dataset.themeOption;
      if (!themes.has(theme)) return;
      applyTheme(theme);
      // The controls still work when browser storage is disabled or full.
      try { localStorage.setItem(storageKey, theme); } catch { /* Current page only. */ }
    }));
    applyTheme(document.documentElement.dataset.theme);
    switcher.hidden = false;
  }, { once: true });

  window.addEventListener('storage', event => {
    if (event.key === storageKey || event.key === null) applyTheme(readTheme());
  });
  window.addEventListener('pageshow', event => {
    if (event.persisted) applyTheme(readTheme());
  });
})();
