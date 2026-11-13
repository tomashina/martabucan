@props(['site', 'pageTitle' => null])
@php
    $settings = $site['settings'];
    $base = \App\Support\SiteSettings::baseUrl($settings);
    $canonical = $base.(request()->path() === '/' ? '/' : '/'.request()->path());
    $seoTitle = $pageTitle ? $pageTitle.' · '.$settings['meta_title'] : $settings['meta_title'];
    $shareTitle = $pageTitle ?: ($settings['og_title'] ?: $settings['meta_title']);
    $shareDescription = $settings['og_description'] ?: $settings['meta_description'];
    $image = \App\Support\ImageAsset::attributes($settings['og_image'] ?: $site['media']['hero'][0]['image']);
@endphp
<meta name="robots" content="{{ \App\Support\SiteSettings::indexable($settings) ? 'index, follow' : 'noindex, nofollow' }}">
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $settings['meta_description'] }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:site_name" content="{{ $settings['site_name'] }}">
<meta property="og:title" content="{{ $shareTitle }}">
<meta property="og:description" content="{{ $shareDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $base.$image['src'] }}">
<meta property="og:image:width" content="{{ $image['width'] }}">
<meta property="og:image:height" content="{{ $image['height'] }}">
<meta property="og:image:alt" content="{{ $settings['og_image_alt'] }}">
<meta property="og:locale" content="hr_HR">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $shareTitle }}">
<meta name="twitter:description" content="{{ $shareDescription }}">
<meta name="twitter:image" content="{{ $base.$image['src'] }}">
@if($settings['google_verification'])<meta name="google-site-verification" content="{{ $settings['google_verification'] }}">@endif
@if($settings['facebook_verification'])<meta name="facebook-domain-verification" content="{{ $settings['facebook_verification'] }}">@endif
