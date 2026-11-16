<!doctype html>
<html lang="{{ app()->getLocale() }}">
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
    <link rel="stylesheet" href="/assets/css/site.css?v=6">
    <link rel="stylesheet" href="/assets/css/editorial.css?v=31">
    @stack('styles')
    <script src="/assets/js/consent.js?v=3" defer></script>
    <script src="/assets/js/site.js?v=6" defer></script>
    @stack('scripts')
</head>
@php $ga4Id = preg_match('/^G-[A-Z0-9]+$/', $site['settings']['ga4_id']) ? $site['settings']['ga4_id'] : ''; @endphp
<body class="@yield('body-class')" @if($ga4Id) data-ga4-id="{{ $ga4Id }}" @endif>
<a href="#main" class="skip-link">{{ $site['ui']['skip_content'] }}</a>
<header class="site-header">
    <div class="container header-inner">
        <x-brand :site="$site" />
        <nav aria-label="{{ $site['ui']['nav_label'] }}" id="main-nav" class="main-nav">
            <a href="{{ \App\Support\SiteLocale::url('home') }}#o-nama">{{ $site['ui']['nav_about'] }}</a><a href="{{ \App\Support\SiteLocale::url('home') }}#usluge">{{ $site['ui']['nav_services'] }}</a><a href="{{ \App\Support\SiteLocale::url('home') }}#tim">{{ $site['ui']['nav_team'] }}</a><a href="{{ \App\Support\SiteLocale::url('pricing') }}">{{ $site['ui']['nav_pricing'] }}</a><a href="{{ \App\Support\SiteLocale::url('home') }}#kontakt">{{ $site['ui']['nav_contact'] }}</a>
            <a class="button menu-cta" href="{{ \App\Support\SiteLocale::url('home') }}#kontakt">{{ $site['ui']['book_exam'] }}</a>
        </nav>
        <x-language-switch :site="$site" />
        <a class="button button-small header-cta" href="{{ \App\Support\SiteLocale::url('home') }}#kontakt">{{ $site['ui']['book_exam'] }}</a>
        <button class="menu-toggle" aria-label="{{ $site['ui']['menu_open'] }}" data-open-label="{{ $site['ui']['menu_open'] }}" data-close-label="{{ $site['ui']['menu_close'] }}" aria-expanded="false" aria-controls="main-nav"><x-icon name="bars" /></button>
    </div>
</header>
<main id="main">@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-top"><x-brand :site="$site" /><p>{{ $site['intro']['footer_text'] }}</p><a class="back-top" href="#main">{{ $site['ui']['back_top'] }} </a></div>
    <div class="container footer-bottom">
        <small>© {{ date('Y') }} Marta Bućan. {{ $site['ui']['copyright'] }}</small>
        @if($site['settings']['facebook_url'] || $site['settings']['instagram_url'])<nav aria-label="{{ $site['ui']['social_nav'] }}">@if($site['settings']['facebook_url'])<a href="{{ $site['settings']['facebook_url'] }}" target="_blank" rel="noopener noreferrer">Facebook</a>@endif @if($site['settings']['instagram_url'])<a href="{{ $site['settings']['instagram_url'] }}" target="_blank" rel="noopener noreferrer">Instagram</a>@endif</nav>@endif
        <nav aria-label="{{ $site['ui']['legal_nav'] }}"><a href="{{ \App\Support\SiteLocale::url('business') }}">{{ $site['ui']['business_title'] }}</a><a href="{{ \App\Support\SiteLocale::url('privacy') }}">{{ $site['ui']['privacy_title'] }}</a><a href="{{ \App\Support\SiteLocale::url('cookies') }}">{{ $site['ui']['cookies_title'] }}</a><a href="{{ \App\Support\SiteLocale::url('terms') }}">{{ $site['ui']['terms_title'] }}</a><button class="cookie-settings-footer" type="button" data-cookie-settings aria-haspopup="dialog" aria-controls="cookie-dialog">{{ $site['ui']['cookie_dialog_title'] }}</button></nav>
    </div>
</footer>
<button class="cookie-settings-fab" type="button" data-cookie-settings data-cookie-launcher aria-label="{{ $site['ui']['cookie_dialog_title'] }}" title="{{ $site['ui']['cookie_dialog_title'] }}" aria-haspopup="dialog" aria-controls="cookie-dialog" hidden><x-icon name="cookie-bite" /></button>
<aside class="cookie-banner" id="cookie-banner" aria-label="{{ $site['ui']['privacy_eyebrow'] }}" hidden>
    <div class="cookie-copy"><x-icon name="cookie-bite" /><div><strong>{{ $site['ui']['cookie_heading'] }}</strong><p>{{ str_replace('{services}', $ga4Id ? $site['ui']['cookie_services_analytics'] : 'Google Maps', $site['ui']['cookie_intro']) }} <a href="{{ \App\Support\SiteLocale::url('cookies') }}">{{ $site['ui']['cookie_about'] }}</a></p></div></div>
    <div class="cookie-actions"><button class="button" data-consent="necessary">{{ $site['ui']['cookie_reject'] }}</button><button class="button" data-consent="all">{{ $site['ui']['cookie_accept'] }}</button><button class="text-button" data-cookie-settings>{{ $site['ui']['cookie_settings'] }}</button></div>
</aside>
<dialog id="cookie-dialog" class="cookie-dialog" aria-labelledby="cookie-title">
    <div class="dialog-header"><span class="eyebrow">{{ $site['ui']['privacy_eyebrow'] }}</span><button class="icon-button" data-close-dialog aria-label="{{ $site['ui']['close_settings'] }}"><x-icon name="xmark" /></button></div>
    <h2 id="cookie-title">{{ $site['ui']['cookie_dialog_title'] }}</h2><p>{{ $site['ui']['cookie_dialog_intro'] }}</p>
    <div class="consent-option"><div><strong>{{ $site['ui']['essential_title'] }}</strong><p>{{ $site['ui']['essential_description'] }}</p></div><span class="always-on">{{ $site['ui']['always_on'] }}</span></div>
    <label class="consent-option"><span><strong>Google Maps</strong><span class="option-detail">{{ $site['ui']['maps_description'] }}</span></span><input id="maps-consent" type="checkbox" role="switch" aria-label="{{ $site['ui']['maps_allow'] }}"></label>
    @if($ga4Id)<label class="consent-option"><span><strong>Google Analytics</strong><span class="option-detail">{{ $site['ui']['analytics_description'] }}</span></span><input id="analytics-consent" type="checkbox" role="switch" aria-label="{{ $site['ui']['analytics_allow'] }}"></label>@endif
    <p class="small">{{ $site['ui']['cookie_retention'] }} <a href="{{ \App\Support\SiteLocale::url('cookies') }}">{{ $site['ui']['cookie_policy'] }}</a> · <a href="{{ \App\Support\SiteLocale::url('privacy') }}">{{ $site['ui']['privacy_title'] }}</a></p>
    <div class="dialog-actions"><button class="button" data-consent="necessary">{{ $site['ui']['cookie_reject'] }}</button><button class="button" id="save-consent">{{ $site['ui']['save_choice'] }}</button></div>
</dialog>
<noscript><div class="noscript-notice">{{ $site['ui']['noscript'] }}</div></noscript>
</body>
</html>
