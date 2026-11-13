(() => {
  'use strict';
  const storageKey = 'mb_consent_v2';
  const lifetime = 180 * 24 * 60 * 60 * 1000;
  const banner = document.querySelector('#cookie-banner');
  const launcher = document.querySelector('[data-cookie-launcher]');
  const dialog = document.querySelector('#cookie-dialog');
  if (!banner || !dialog) return;
  const mapsToggle = document.querySelector('#maps-consent');
  const analyticsToggle = document.querySelector('#analytics-consent');
  const map = document.querySelector('#map-container');
  const placeholder = document.querySelector('#map-placeholder');
  const gaId = /^G-[A-Z0-9]+$/.test(document.body.dataset.ga4Id || '') ? document.body.dataset.ga4Id : '';
  // Changing the configured service renews consent instead of extending an older choice.
  const policy = `2026-11-12:${gaId || 'no-analytics'}`;
  let consent = null;
  let analyticsStarted = false;
  let consentTimer;
  let settingsTrigger;
  const valid = value => value?.version === 2 && value.policy === policy
    && typeof value.maps === 'boolean' && typeof value.analytics === 'boolean'
    && Number.isFinite(value.timestamp) && value.timestamp <= Date.now()
    && Date.now() - value.timestamp < lifetime;
  try {
    const saved = JSON.parse(localStorage.getItem(storageKey));
    if (valid(saved)) consent = saved;
    else localStorage.removeItem(storageKey);
    localStorage.removeItem('mb_consent_v1');
  } catch { /* Choice remains usable in memory when storage is blocked. */ }

  const clearAnalyticsCookies = () => {
    const domains = ['', ...location.hostname.split('.').map((_, i, parts) => parts.slice(i).join('.')).filter(value => value.includes('.'))];
    document.cookie.split(';').map(cookie => cookie.trim().split('=')[0])
      .filter(name => /^(_ga(?:_|$)|_gid$|_gat(?:_|$))/.test(name))
      .forEach(name => domains.forEach(domain => {
        document.cookie = `${name}=; Max-Age=0; Path=/; SameSite=Lax${domain ? `; Domain=${domain}` : ''}${location.protocol === 'https:' ? '; Secure' : ''}`;
      }));
  };
  const setAnalytics = allowed => {
    if (!gaId) return false;
    window[`ga-disable-${gaId}`] = !allowed;
    if (!allowed) {
      clearAnalyticsCookies();
      if (analyticsStarted) {
        window.gtag('consent', 'update', { analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
        // Removing a script element cannot unload its listeners. Reload after saving
        // withdrawal so no previously loaded Google code continues running.
        return true;
      }
      return false;
    }
    if (analyticsStarted) return false;
    analyticsStarted = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('consent', 'default', { analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
    window.gtag('consent', 'update', { analytics_storage: 'granted', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
    window.gtag('js', new Date());
    window.gtag('config', gaId, {
      allow_google_signals: false,
      allow_ad_personalization_signals: false,
      cookie_expires: lifetime / 1000,
      cookie_update: false,
      cookie_flags: `SameSite=Lax${location.protocol === 'https:' ? ';Secure' : ''}`,
      page_location: location.origin + location.pathname,
      page_referrer: '',
      ignore_referrer: true,
    });
    const script = document.createElement('script');
    script.id = 'ga4-script';
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(gaId)}`;
    script.referrerPolicy = 'no-referrer';
    document.head.appendChild(script);
    return false;
  };
  const updateMap = () => {
    if (!map || !placeholder) return;
    const iframe = map.querySelector('iframe');
    if (consent?.maps) {
      if (!iframe) {
        const frame = document.createElement('iframe');
        frame.title = map.dataset.mapTitle || 'Google Maps';
        frame.src = map.dataset.mapSrc;
        frame.loading = 'lazy';
        frame.referrerPolicy = 'no-referrer';
        frame.allowFullscreen = true;
        map.appendChild(frame);
      }
      placeholder.hidden = true;
    } else {
      iframe?.remove();
      placeholder.hidden = false;
    }
  };
  const applyChoice = () => {
    banner.hidden = !!consent;
    if (launcher) launcher.hidden = !consent;
    mapsToggle.checked = consent?.maps === true;
    if (analyticsToggle) analyticsToggle.checked = consent?.analytics === true;
    updateMap();
    clearTimeout(consentTimer);
    if (consent) {
      const remaining = lifetime - (Date.now() - consent.timestamp);
      consentTimer = setTimeout(expireChoice, Math.min(remaining + 1, 2147483647));
    }
    if (setAnalytics(!!gaId && consent?.analytics === true)) location.reload();
  };
  const expireChoice = () => {
    if (consent && !valid(consent)) {
      consent = null;
      try { localStorage.removeItem(storageKey); } catch { /* Memory-only mode. */ }
    }
    applyChoice();
  };
  const saveChoice = (maps, analytics) => {
    consent = { version: 2, policy, maps: !!maps, analytics: !!gaId && !!analytics, timestamp: Date.now() };
    try { localStorage.setItem(storageKey, JSON.stringify(consent)); } catch { /* Memory-only mode. */ }
    if (dialog.open) dialog.close();
    applyChoice();
  };
  document.querySelectorAll('[data-consent]').forEach(button => button.addEventListener('click', () => saveChoice(button.dataset.consent === 'all', button.dataset.consent === 'all')));
  document.querySelectorAll('[data-cookie-settings]').forEach(button => button.addEventListener('click', () => {
    settingsTrigger = button;
    mapsToggle.checked = consent?.maps === true;
    if (analyticsToggle) analyticsToggle.checked = consent?.analytics === true;
    dialog.showModal();
  }));
  document.querySelectorAll('[data-close-dialog]').forEach(button => button.addEventListener('click', () => dialog.close()));
  document.querySelector('#save-consent')?.addEventListener('click', () => saveChoice(mapsToggle.checked, analyticsToggle?.checked));
  // Enabling the map never grants analytics consent.
  document.querySelectorAll('[data-enable-map]').forEach(button => button.addEventListener('click', () => saveChoice(true, consent?.analytics === true)));
  dialog.addEventListener('close', () => { if (settingsTrigger?.offsetParent) settingsTrigger.focus(); });
  dialog.addEventListener('click', event => {
    if (event.target !== dialog) return;
    const rect = dialog.getBoundingClientRect();
    if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
  });
  window.addEventListener('storage', event => {
    if (event.key !== storageKey && event.key !== null) return;
    try { const value = JSON.parse(event.newValue); consent = valid(value) ? value : null; } catch { consent = null; }
    applyChoice();
  });
  document.addEventListener('visibilitychange', () => { if (!document.hidden) expireChoice(); });
  window.addEventListener('pageshow', expireChoice);
  applyChoice();
})();
