@props(['eyebrow', 'title' => ''])
<section class="page-heading tinted-section" aria-labelledby="page-title">
    <div class="container">
        <a class="text-link page-back" href="/">Povratak na naslovnicu</a>
        <p class="section-number">{{ $eyebrow }}</p>
        <h1 id="page-title">{{ $heading ?? $title }}</h1>
        <div class="page-intro">{{ $slot }}</div>
    </div>
</section>
