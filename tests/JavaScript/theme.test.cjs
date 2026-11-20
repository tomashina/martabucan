// Run with: node --test tests/JavaScript/theme.test.cjs
const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { resolve } = require('node:path');
const { runInNewContext } = require('node:vm');
const source = readFileSync(resolve(__dirname, '../../public/assets/js/theme.js'), 'utf8');

function loadPage({ saved = null, blocked = false } = {}) {
  const stored = new Map(saved === null ? [] : [['mb_color_theme', saved]]);
  const events = {};
  const makeElement = dataset => ({
    dataset, attributes: {}, handlers: {},
    setAttribute(name, value) { this.attributes[name] = value; },
    addEventListener(name, handler) { this.handlers[name] = handler; },
  });
  const root = makeElement({});
  const meta = makeElement({});
  const favicon = makeElement({});
  const choices = ['olive', 'cherry', 'gold'].map(themeOption => makeElement({ themeOption }));
  const switcher = { hidden: true, querySelectorAll: () => choices };
  const document = {
    documentElement: root,
    querySelector(selector) {
      return { 'meta[name="theme-color"]': meta, 'link[rel="icon"]': favicon, '[data-theme-switcher]': switcher }[selector];
    },
    addEventListener(name, handler) { events[name] = handler; },
  };
  const localStorage = {
    getItem(key) { if (blocked) throw new Error('Storage blocked'); return stored.get(key) ?? null; },
    setItem(key, value) { if (blocked) throw new Error('Storage blocked'); stored.set(key, value); },
  };
  runInNewContext(source, {
    document, localStorage,
    window: { addEventListener(name, handler) { events[name] = handler; } },
  });
  return { root, meta, favicon, choices, switcher, stored, events,
    ready: () => events.DOMContentLoaded(),
    click: theme => choices.find(button => button.dataset.themeOption === theme).handlers.click(),
  };
}

test('restores each saved theme before the body is ready', () => {
  for (const saved of ['olive', 'cherry', 'gold']) {
    const page = loadPage({ saved });
    assert.equal(page.root.dataset.theme, saved);
    assert.equal(page.favicon.attributes.href, `/favicon-${saved}.png`);
    assert.equal(page.switcher.hidden, true);
    page.ready();
    assert.equal(page.switcher.hidden, false);
    assert.equal(page.choices.filter(button => button.attributes['aria-pressed'] === 'true').length, 1);
    assert.equal(page.choices.find(button => button.attributes['aria-pressed'] === 'true').dataset.themeOption, saved);
  }
});

test('clicks update theme, browser color, favicon and selection, then survive navigation', () => {
  const page = loadPage();
  page.ready();
  for (const [theme, color] of [['cherry', '#762E40'], ['gold', '#B59960'], ['olive', '#636B2F']]) {
    page.click(theme);
    assert.equal(page.root.dataset.theme, theme);
    assert.equal(page.meta.attributes.content, color);
    assert.equal(page.favicon.attributes.href, `/favicon-${theme}.png`);
    assert.equal(page.stored.get('mb_color_theme'), theme);
    for (const button of page.choices) {
      assert.equal(button.attributes['aria-pressed'], String(button.dataset.themeOption === theme));
    }
    assert.equal(loadPage({ saved: page.stored.get('mb_color_theme') }).root.dataset.theme, theme);
  }
});

test('missing or invalid preferences fall back safely without writing storage', () => {
  for (const saved of [null, '', 'purple', '__proto__', 'constructor', '../../unexpected']) {
    const page = loadPage({ saved });
    page.ready();
    assert.equal(page.root.dataset.theme, 'olive');
    assert.equal(page.favicon.attributes.href, '/favicon-olive.png');
    assert.equal(page.stored.get('mb_color_theme') ?? null, saved);
  }
});

test('blocked browser storage does not stop switching', () => {
  const page = loadPage({ blocked: true });
  page.ready();
  page.click('gold');
  assert.equal(page.root.dataset.theme, 'gold');
  assert.equal(page.stored.size, 0);
});

test('other tabs and browser back restore the current stored preference', () => {
  const page = loadPage();
  page.ready();
  page.stored.set('mb_color_theme', 'cherry');
  page.events.storage({ key: 'mb_consent_v2' });
  assert.equal(page.root.dataset.theme, 'olive');
  page.events.storage({ key: 'mb_color_theme' });
  assert.equal(page.root.dataset.theme, 'cherry');
  page.stored.set('mb_color_theme', 'gold');
  page.events.pageshow({ persisted: true });
  assert.equal(page.root.dataset.theme, 'gold');
  page.stored.clear();
  page.events.storage({ key: null });
  assert.equal(page.root.dataset.theme, 'olive');
});
