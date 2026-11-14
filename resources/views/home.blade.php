@extends('layouts.site')
@section('body-class', 'home-page')
@push('styles')
<link rel="stylesheet" href="/assets/css/hero.css?v=6">
@endpush
@push('scripts')
<script src="/assets/js/hero.js?v=3" defer></script>
@endpush
@section('content')
<section class="hero hero-cover" aria-labelledby="hero-title" data-hero-slider>
    <div class="hero-slides" role="group" aria-label="{{ $site['ui']['gallery_photos'] }}" aria-live="off">
        @foreach($site['media']['hero'] as $slide)
        <picture class="hero-backdrop {{ $loop->first ? 'is-active' : '' }} {{ str_contains($slide['image'], 'hero-room-') ? 'hero-room' : '' }} {{ str_contains($slide['image'], 'hero-detail-') ? 'hero-detail' : '' }}" id="hero-slide-{{ $loop->iteration }}" data-hero-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
            @if($slide['image'] === '/assets/images/hero-1280.webp')<source media="(max-width: 640px)" srcset="/assets/images/hero-mobile.webp">@endif
            <x-site-image :path="$slide['image']" :alt="$slide['alt']" :loading="$loop->first ? 'eager' : 'lazy'" :priority="$loop->first ? 'high' : 'low'" />
        </picture>
        @endforeach
    </div>
    <div class="hero-shade" aria-hidden="true"></div>
    <div class="container hero-cover-inner">
        <div class="hero-copy">
            <p class="eyebrow">{{ $site['intro']['eyebrow'] }}</p>
            <h1 id="hero-title">{{ $site['intro']['title'] }} <span>{{ $site['intro']['accent'] }}</span></h1>
        </div>
        <div class="hero-footer">
            <div class="hero-actions">
                <a class="button" href="#kontakt">{{ $site['intro']['hero_primary'] }}</a>
                <a class="button hero-explore" href="#usluge">{{ $site['intro']['hero_secondary'] }}</a>
            </div>
            <div class="hero-controls" role="group" aria-label="{{ $site['ui']['gallery_controls'] }}" hidden>
                <button class="hero-playback" type="button" data-hero-toggle data-pause-label="{{ $site['ui']['slideshow_pause'] }}" data-play-label="{{ $site['ui']['slideshow_play'] }}" aria-label="{{ $site['ui']['slideshow_pause'] }}" title="{{ $site['ui']['slideshow_pause'] }}"><span data-hero-pause-icon><x-icon name="pause" /></span><span data-hero-play-icon hidden><x-icon name="play" /></span></button>
                @foreach($site['media']['hero'] as $slide)
                <button class="hero-dot" type="button" data-hero-dot aria-controls="hero-slide-{{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" aria-label="{{ $site['ui']['show_photo'] }} {{ $slide['alt'] }}"><span></span></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section about" id="o-nama" aria-labelledby="about-title">
    <div class="container about-editorial">
        <div class="about-photo photo-depth">
            <div class="photo-depth-frame">
                <x-site-image :path="$site['media']['about']" :alt="$site['media']['about_alt']" sizes="(max-width: 1000px) 90vw, (max-width: 1400px) 45vw, 610px" loading="eager" />
            </div>
        </div>
        <div class="about-content">
            <p class="section-number">{{ $site['intro']['about_eyebrow'] }}</p>
            <h2 id="about-title">{{ $site['intro']['about_title'] }}</h2>
            <x-rich-text :text="$site['intro']['about_html']" first-class="lead" />
        </div>
    </div>
</section>

<section class="principles-band tinted-section" id="pristup" aria-label="{{ $site['ui']['approach_label'] }}">
    <div class="container principles">
        <article class="principle-card">
            <span class="principle-icon"><x-icon name="user-doctor" /></span>
            <h3>{{ $site['intro']['expertise_title'] }}</h3>
            <x-rich-text :text="$site['intro']['expertise']" />
        </article>
        <article class="principle-card">
            <span class="principle-icon"><x-icon name="badge-check" /></span>
            <h3>{{ $site['intro']['quality_title'] }}</h3>
            <x-rich-text :text="$site['intro']['quality']" />
        </article>
        <article class="principle-card">
            <span class="principle-icon"><x-icon name="heart" /></span>
            <h3>{{ $site['intro']['dedication_title'] }}</h3>
            <x-rich-text :text="$site['intro']['dedication']" />
        </article>
    </div>
</section>

<section class="section services" id="usluge" aria-labelledby="services-title">
    <div class="container services-editorial">
        <div class="services-intro">
            <p class="section-number">{{ $site['intro']['services_eyebrow'] }}</p>
            <h2 id="services-title">{{ $site['intro']['services_title'] }} <span class="serif-accent">{{ $site['intro']['services_accent'] }}</span></h2>
            <x-rich-text :text="$site['intro']['services_text']" />
            <div class="services-note"><x-icon name="heart" /><span>{{ $site['intro']['services_note'] }}</span></div>
        </div>
        <div class="service-list">
            @foreach($site['services'] as $service)
            <details class="service-row" name="services">
                <summary>
                    <span class="service-icon"><x-icon :name="$service['icon']" /></span>
                    <h3>{{ $service['title'] }}</h3>
                    <span class="service-toggle"><x-icon name="plus" /></span>
                </summary>
                <div class="service-description">
                    <x-rich-text :text="$service['description']" />
                    <a href="#kontakt">{{ $site['ui']['book_exam'] }}</a>
                </div>
            </details>
            @endforeach
            <div class="service-help"><p>{{ $site['ui']['service_help'] }} <a href="#kontakt">{{ $site['ui']['talk_to_us'] }}</a></p></div>
        </div>
    </div>
</section>

<section class="section spaces tinted-section" id="prostor" aria-labelledby="spaces-title">
    <div class="container">
        <div class="spaces-heading">
            <p class="section-number">{{ $site['intro']['spaces_eyebrow'] }}</p>
            <h2 id="spaces-title">{{ $site['intro']['spaces_title'] }} <span class="serif-accent">{{ $site['intro']['spaces_accent'] }}</span></h2>
            <x-rich-text :text="$site['intro']['spaces_text']" />
        </div>
        <div class="space-photos">
            @foreach($site['media']['spaces'] as $photo)
            <figure>
                <div class="space-photo photo-depth"><div class="photo-depth-frame">
                    <x-site-image :path="$photo['image']" :alt="$photo['alt']" sizes="(max-width: 640px) calc(100vw - 40px), (max-width: 1376px) calc((100vw - 136px) / 2), 620px" />
                </div></div>
            </figure>
            @endforeach
        </div>
    </div>
</section>

<section class="section team" id="tim" aria-labelledby="team-title">
    <div class="container">
        <div class="team-heading">
            <p class="section-number">{{ $site['intro']['team_eyebrow'] }}</p>
            <h2 id="team-title">{{ $site['intro']['team_title'] }} <span class="serif-accent">{{ $site['intro']['team_accent'] }}</span></h2>
            <x-rich-text :text="$site['intro']['team_text']" />
        </div>
        <div class="team-grid">
            @foreach($site['team'] as $member)
            <article class="team-member">
                <div class="team-photo photo-depth">
                    <div class="photo-depth-frame">
                        <x-site-image :path="$member['image']" :alt="$member['name']" sizes="(max-width: 370px) calc((100vw - 52px) / 2), (max-width: 640px) calc((100vw - 60px) / 2), (max-width: 1100px) calc((100vw - 96px) / 2), (max-width: 1376px) calc((100vw - 192px) / 4), 296px" />
                    </div>
                </div>
                <div class="member-copy">
                    <h3>{{ $member['name'] }}</h3>
                    <p class="team-role">{{ $member['role'] }}</p>
                    <div class="team-bio"><x-rich-text :text="$member['bio']" /></div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section results tinted-section" id="rezultati" aria-labelledby="results-title">
    <div class="container">
        <div class="results-heading">
            <p class="section-number">{{ $site['intro']['results_eyebrow'] }}</p>
            <h2 id="results-title">{{ $site['intro']['results_title'] }} <span class="serif-accent">{{ $site['intro']['results_accent'] }}</span></h2>
            <x-rich-text :text="$site['intro']['results_html']" first-class="results-lead" />
        </div>
        <div class="results-grid">
            @foreach($site['media']['results'] as $photo)
            <figure>
                <div class="result-photo photo-depth">
                    <button class="result-zoom photo-depth-frame" type="button" data-gallery="{{ \App\Support\ImageAsset::url($photo['image']) }}" data-caption="{{ $photo['caption'] }}" aria-label="{{ $site['ui']['zoom_photo'] }}">
                        <x-site-image :path="$photo['image']" :alt="$photo['alt']" sizes="(max-width: 640px) calc(100vw - 40px), (max-width: 1376px) calc((100vw - 128px) / 2), 624px" />
                        <span class="zoom-label"><x-icon name="magnifying-glass-plus" /> {{ $site['ui']['zoom_label'] }}</span>
                    </button>
                </div>
                <figcaption><span>{{ $site['ui']['before'] }}</span><span>{{ $site['ui']['after'] }}</span></figcaption>
            </figure>
            @endforeach
        </div>
        <p class="results-note">{{ $site['intro']['results_note'] }}</p>
    </div>
</section>
<dialog class="gallery-dialog" id="gallery-dialog" aria-labelledby="gallery-caption"><button class="icon-button" data-close-gallery aria-label="{{ $site['ui']['close_photo'] }}"><x-icon name="xmark" /></button><img id="gallery-image" alt=""><p id="gallery-caption"></p></dialog>

<section class="section faq faq-editorial" id="pitanja" aria-labelledby="faq-title"><div class="container faq-grid"><div><p class="section-number">{{ $site['intro']['faq_eyebrow'] }}</p><h2 id="faq-title">{{ $site['intro']['faq_title'] }} <span class="serif-accent">{{ $site['intro']['faq_accent'] }}</span></h2></div><div class="faq-list">
    @foreach($site['faq'] as $question)
    <details><summary>{{ $question['question'] }}<x-icon name="plus" /></summary><x-rich-text :text="strtr($question['answer'], ['{phone}'=>e($site['contact']['phone']), '{email}'=>e($site['contact']['email']), '{address}'=>e($site['contact']['address']), '{city}'=>e($site['contact']['city'])])" /></details>
    @endforeach
</div></div></section>

<section class="section contact tinted-section" id="kontakt" aria-labelledby="contact-title">
    <div class="container">
        <div class="contact-heading"><p class="section-number">{{ $site['intro']['contact_eyebrow'] }}</p><h2 id="contact-title">{{ $site['intro']['contact_title'] }} <span class="serif-accent">{{ $site['intro']['contact_accent'] }}</span></h2><x-rich-text :text="$site['intro']['original_heading']" /></div>
        <div class="contact-grid"><div class="contact-info">
            <a class="contact-phone" href="tel:{{ preg_replace('/[^+0-9]/', '', $site['contact']['phone']) }}"><x-icon name="phone" />{{ $site['contact']['phone'] }}</a>
            <a class="contact-email" href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a>
            <div class="contact-details"><div><x-icon name="location-dot" /><h3>{{ $site['ui']['address_label'] }}</h3><p>{{ $site['contact']['address'] }} {{ $site['contact']['city'] }}</p></div><div><x-icon name="clock" /><h3>{{ $site['ui']['hours_label'] }}</h3><dl><dt>{{ $site['contact']['hours_afternoon'] }}</dt><dd>{{ $site['contact']['afternoon_time'] }}</dd><dt>{{ $site['contact']['hours_morning'] }}</dt><dd>{{ $site['contact']['morning_time'] }}</dd></dl></div></div>
            <p class="payment-note"><x-icon name="credit-card" />{{ $site['contact']['payment_note'] }}</p><p class="contact-note"><x-icon name="circle-info" />{{ $site['contact']['hours_note'] }}</p>
        </div><div class="map-card">
            <div id="map-container" class="map-container" data-map-title="{{ $site['ui']['map_title'] }}" data-map-src="{{ $site['settings']['maps_url'] }}">
                <div class="map-placeholder" id="map-placeholder"><span class="map-pin"><x-icon name="location-dot" /></span><strong>{{ $site['ui']['map_headline'] }}</strong><span>{{ $site['contact']['address'] }}, Zagreb</span><button class="button button-small" data-enable-map>{{ $site['ui']['enable_map'] }} </button><p>{{ $site['ui']['map_consent'] }} <a href="{{ \App\Support\SiteLocale::url('privacy') }}">{{ $site['ui']['privacy_title'] }}</a></p></div>
            </div><a class="map-directions" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ rawurlencode($site['contact']['address'].', '.$site['contact']['city']) }}" target="_blank" rel="noopener noreferrer">{{ $site['ui']['directions'] }} <span>{{ $site['ui']['open_maps'] }} </span></a>
        </div></div>
    </div>
</section>
@endsection
