@extends('layouts.site')
@section('body-class', 'interior-page')
@push('styles')<link rel="stylesheet" href="/assets/css/pages.css?v={{ substr(hash_file('sha256', public_path('assets/css/pages.css')), 0, 16) }}">@endpush
@section('content')
@php
    $pages = ['business', 'privacy', 'cookies', 'terms'];
@endphp
<x-page-heading :site="$site" :eyebrow="$site['ui']['visitor_info']" :title="$title"><x-rich-text :text="$site['page_meta'][$key]['intro']" /></x-page-heading>
<section class="section information-page">
    <div class="container information-grid">
        <nav class="information-nav" aria-label="{{ $site['ui']['visitor_info'] }}">
            @foreach($pages as $pageKey)
                <a href="{{ \App\Support\SiteLocale::url($pageKey) }}" @if($key === $pageKey) aria-current="page" @endif>{{ $site['ui'][$pageKey.'_title'] }}</a>
            @endforeach
        </nav>
        <article class="information-content" aria-label="{{ $title }}">
            @if($key === 'business')
                <dl class="business-data">
                    @foreach(['name', 'oib', 'register', 'authorization', 'chamber', 'professional_title', 'qualification_country', 'professional_rules', 'supervisory_authority'] as $field)
                        @if(filled($site['business'][$field]))<div><dt>{{ $site['ui']['business_'.$field] }}</dt><dd>{{ $site['business'][$field] }}</dd></div>@endif
                    @endforeach
                    <div><dt>{{ $site['ui']['business_address'] }}</dt><dd>{{ $site['contact']['address'] }}, {{ $site['contact']['city'] }}</dd></div>
                    <div><dt>{{ $site['ui']['business_phone'] }}</dt><dd><a href="tel:{{ preg_replace('/[^+0-9]/', '', $site['contact']['phone']) }}">{{ $site['contact']['phone'] }}</a></dd></div>
                    <div><dt>E-mail</dt><dd><a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a></dd></div>
                </dl>
                <section class="information-block"><h2>{{ $site['ui']['complaints_title'] }}</h2><p>{{ $site['business']['complaints'] }}</p></section>
            @else
                @if($key === 'privacy')
                    <section class="information-block controller-card">
                        <h2>{{ $site['ui']['data_controller'] }}</h2>
                        <p>{{ $site['business']['name'] }}<br>{{ $site['contact']['address'] }}, {{ $site['contact']['city'] }}@if($site['business']['oib'])<br>OIB: {{ $site['business']['oib'] }}@endif</p>
                        <a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a>
                    </section>
                @endif
                <div class="legal-text">
                    <x-rich-text :text="$site['legal'][$key]" :headings="true" />
                </div>
                @if($key === 'cookies')
                    <section class="information-block">
                        <h2>{{ $site['ui']['theme_storage_title'] }}</h2>
                        <p>{{ $site['ui']['theme_storage_description'] }}</p>
                    </section>
                @endif
                @if($key === 'privacy' && $site['business']['hosting_provider'])<section class="information-block"><h2>{{ $site['ui']['hosting_provider'] }}</h2><p>{{ $site['business']['hosting_provider'] }}</p></section>@endif
                @if($key === 'privacy' && $site['business']['privacy_retention'])<section class="information-block"><h2>{{ $site['ui']['retention_title'] }}</h2><p>{{ $site['business']['privacy_retention'] }}</p></section>@endif
                @if(in_array($key, ['privacy', 'cookies']))
                    <section class="information-block">
                        <h2>Google Analytics</h2>
                        @if(preg_match('/^G-[A-Z0-9]+$/', $site['settings']['ga4_id']))
                            <p>{{ $site['ui']['analytics_active'] }}</p>
                        @else
                            <p>{{ $site['ui']['analytics_inactive'] }}</p>
                        @endif
                    </section>
                    <p class="provider-links"><a href="https://policies.google.com/privacy?hl={{ app()->getLocale() }}" target="_blank" rel="noopener noreferrer">{{ $site['ui']['google_privacy'] }}</a> · <a href="https://policies.google.com/technologies/cookies?hl={{ app()->getLocale() }}" target="_blank" rel="noopener noreferrer">{{ $site['ui']['google_cookies'] }}</a></p>
                @endif
                @if($key === 'privacy')<a class="text-link" href="https://azop.hr/prava-ispitanika-2/" target="_blank" rel="noopener noreferrer">{{ $site['ui']['azop_rights'] }}</a>@endif
                @if($key === 'cookies')<button class="button" type="button" data-cookie-settings>{{ $site['ui']['change_cookies'] }}</button>@endif
            @endif
        </article>
    </div>
</section>
@endsection
