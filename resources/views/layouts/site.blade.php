<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#775686">
    <x-site-meta :site="$site" :page-title="$title ?? null" />
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="preload" href="/assets/fonts/instrument-sans.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/sora.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/light.min.css">
    <link rel="stylesheet" href="/assets/css/site.css?v=5">
    <link rel="stylesheet" href="/assets/css/editorial.css?v=26">
    @stack('styles')
    <script src="/assets/js/consent.js?v=2" defer></script>
    <script src="/assets/js/site.js?v=5" defer></script>
    @stack('scripts')
</head>
@php $ga4Id = preg_match('/^G-[A-Z0-9]+$/', $site['settings']['ga4_id']) ? $site['settings']['ga4_id'] : ''; @endphp
<body class="@yield('body-class')" @if($ga4Id) data-ga4-id="{{ $ga4Id }}" @endif>
<a href="#main" class="skip-link">Preskoči na sadržaj</a>
<header class="site-header">
    <div class="container header-inner">
        <x-brand />
        <nav aria-label="Glavna navigacija" id="main-nav" class="main-nav">
            <a href="/#o-nama">O nama</a><a href="/#usluge">Usluge</a><a href="/#tim">Naš tim</a><a href="/cjenik">Cjenik</a><a href="/#kontakt">Kontakt</a>
            <a class="button menu-cta" href="tel:{{ preg_replace('/[^+0-9]/', '', $site['contact']['phone']) }}"><x-icon name="phone" /> Naručite se</a>
        </nav>
        <a class="button button-small header-cta" href="tel:{{ preg_replace('/[^+0-9]/', '', $site['contact']['phone']) }}"><x-icon name="phone" /> Naručite se</a>
        <button class="menu-toggle" aria-label="Otvori izbornik" aria-expanded="false" aria-controls="main-nav"><x-icon name="bars" /></button>
    </div>
</header>
<main id="main">@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-top"><x-brand /><p>{{ $site['intro']['footer_text'] }}</p><a class="back-top" href="#main">Povratak na vrh </a></div>
    <div class="container footer-bottom">
        <small>© {{ date('Y') }} Marta Bućan. Sva prava pridržana.</small>
        @if($site['settings']['facebook_url'] || $site['settings']['instagram_url'])<nav aria-label="Društvene mreže">@if($site['settings']['facebook_url'])<a href="{{ $site['settings']['facebook_url'] }}" target="_blank" rel="noopener noreferrer">Facebook</a>@endif @if($site['settings']['instagram_url'])<a href="{{ $site['settings']['instagram_url'] }}" target="_blank" rel="noopener noreferrer">Instagram</a>@endif</nav>@endif
        <nav aria-label="Pravne informacije"><a href="/podaci-o-ordinaciji">Podaci o ordinaciji</a><a href="/privatnost">Privatnost</a><a href="/kolacici">Kolačići</a><a href="/uvjeti-koristenja">Uvjeti korištenja</a></nav>
    </div>
</footer>
<button class="cookie-settings-fab" type="button" data-cookie-settings data-cookie-launcher aria-label="Postavke kolačića" title="Postavke kolačića" aria-haspopup="dialog" aria-controls="cookie-dialog" hidden><x-icon name="cookie-bite" /></button>
<aside class="cookie-banner" id="cookie-banner" aria-label="Vaša privatnost" hidden>
    <div class="cookie-copy"><x-icon name="cookie-bite" /><div><strong>Kolačići. Vaš izbor.</strong><p>Nužna pohrana omogućuje rad stranice. Google Maps{{ $ga4Id ? ' i Google Analytics' : '' }} uključujemo samo ako to dopustite. Odbijanje ne ograničava pregledavanje stranice. <a href="/kolacici">O kolačićima</a></p></div></div>
    <div class="cookie-actions"><button class="button" data-consent="necessary">Odbij neobavezne</button><button class="button" data-consent="all">Prihvati sve</button><button class="text-button" data-cookie-settings>Postavke</button></div>
</aside>
<dialog id="cookie-dialog" class="cookie-dialog" aria-labelledby="cookie-title">
    <div class="dialog-header"><span class="eyebrow">Vaša privatnost</span><button class="icon-button" data-close-dialog aria-label="Zatvori postavke"><x-icon name="xmark" /></button></div>
    <h2 id="cookie-title">Postavke kolačića</h2><p>Odaberite što dopuštate. Neobavezne usluge uključuju se tek uz vaš pristanak. Izbor možete promijeniti ili povući u svakom trenutku.</p>
    <div class="consent-option"><div><strong>Nužna pohrana</strong><p>Pamćenje vašeg izbora, sigurna sesija i zaštita obrazaca. Potrebno za siguran rad stranice.</p></div><span class="always-on">Uvijek aktivno</span></div>
    <label class="consent-option"><span><strong>Google Maps</strong><span class="option-detail">Omogućuje prikaz karte. Podaci se šalju Googleu, koji može postaviti svoje kolačiće.</span></span><input id="maps-consent" type="checkbox" role="switch" aria-label="Dopusti Google Maps"></label>
    @if($ga4Id)<label class="consent-option"><span><strong>Google Analytics</strong><span class="option-detail">Statistika posjeta i korištenja stranice. Google obrađuje mrežne identifikatore i podatke o pregledavanju. Bez oglašavanja i personalizacije.</span></span><input id="analytics-consent" type="checkbox" role="switch" aria-label="Dopusti Google Analytics"></label>@endif
    <p class="small">Izbor pamtimo 180 dana. Odbijanje ne ograničava pristup sadržaju. <a href="/kolacici">Politika kolačića</a> · <a href="/privatnost">Privatnost</a></p>
    <div class="dialog-actions"><button class="button" data-consent="necessary">Odbij neobavezne</button><button class="button" id="save-consent">Spremi izbor</button></div>
</dialog>
<noscript><div class="noscript-notice">Google karta zahtijeva JavaScript i vaš pristanak. Za dolazak koristite poveznicu „Upute za dolazak”.</div></noscript>
</body>
</html>
