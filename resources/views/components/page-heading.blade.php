@props(['eyebrow', 'title' => '', 'site'])
<section class="page-heading tinted-section" aria-labelledby="page-title">
    <div class="container">
        <a class="text-link page-back" href="{{ \App\Support\SiteLocale::url() }}">{{ $site['ui']['back_home'] }}</a>
        <p class="section-number">{{ $eyebrow }}</p>
        <h1 id="page-title">{{ $heading ?? $title }}</h1>
        <div class="page-intro">{{ $slot }}</div>
    </div>
</section>
