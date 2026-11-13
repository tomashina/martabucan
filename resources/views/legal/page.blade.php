@extends('layouts.site')
@section('body-class', 'interior-page')
@push('styles')<link rel="stylesheet" href="/assets/css/pages.css?v={{ substr(hash_file('sha256', public_path('assets/css/pages.css')), 0, 16) }}">@endpush
@section('content')
@php
    $pages = ['business' => ['podaci-o-ordinaciji', 'Podaci o ordinaciji'], 'privacy' => ['privatnost', 'Privatnost'], 'cookies' => ['kolacici', 'Kolačići'], 'terms' => ['uvjeti-koristenja', 'Uvjeti korištenja']];
@endphp
<x-page-heading eyebrow="INFORMACIJE ZA POSJETITELJE" :title="$title"><x-rich-text :text="$site['page_meta'][$key]['intro']" /></x-page-heading>
<section class="section information-page">
    <div class="container information-grid">
        <nav class="information-nav" aria-label="Informacije za posjetitelje">
            @foreach($pages as $pageKey => [$slug, $label])
                <a href="/{{ $slug }}" @if($key === $pageKey) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <article class="information-content" aria-label="{{ $title }}">
            @if($key === 'business')
                <dl class="business-data">
                    @foreach(['name' => 'Naziv ordinacije', 'oib' => 'OIB', 'register' => 'Registar i broj upisa', 'authorization' => 'Odobrenje za obavljanje djelatnosti', 'chamber' => 'Strukovna komora', 'professional_title' => 'Profesionalni naziv', 'qualification_country' => 'Država stjecanja kvalifikacije', 'professional_rules' => 'Profesionalna pravila', 'supervisory_authority' => 'Nadležno nadzorno tijelo'] as $field => $label)
                        @if(filled($site['business'][$field]))<div><dt>{{ $label }}</dt><dd>{{ $site['business'][$field] }}</dd></div>@endif
                    @endforeach
                    <div><dt>Adresa</dt><dd>{{ $site['contact']['address'] }}, {{ $site['contact']['city'] }}</dd></div>
                    <div><dt>Telefon</dt><dd><a href="tel:{{ preg_replace('/[^+0-9]/', '', $site['contact']['phone']) }}">{{ $site['contact']['phone'] }}</a></dd></div>
                    <div><dt>E-mail</dt><dd><a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a></dd></div>
                </dl>
                <section class="information-block"><h2>Pisani prigovori</h2><p>{{ $site['business']['complaints'] }}</p></section>
            @else
                @if($key === 'privacy')
                    <section class="information-block controller-card">
                        <h2>Voditelj obrade</h2>
                        <p>{{ $site['business']['name'] }}<br>{{ $site['contact']['address'] }}, {{ $site['contact']['city'] }}@if($site['business']['oib'])<br>OIB: {{ $site['business']['oib'] }}@endif</p>
                        <a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a>
                    </section>
                @endif
                <div class="legal-text">
                    <x-rich-text :text="$site['legal'][$key]" :headings="true" />
                </div>
                @if($key === 'privacy' && $site['business']['hosting_provider'])<section class="information-block"><h2>Pružatelj hostinga</h2><p>{{ $site['business']['hosting_provider'] }}</p></section>@endif
                @if($key === 'privacy' && $site['business']['privacy_retention'])<section class="information-block"><h2>Rokovi čuvanja</h2><p>{{ $site['business']['privacy_retention'] }}</p></section>@endif
                @if(in_array($key, ['privacy', 'cookies']))
                    <section class="information-block">
                        <h2>Google Analytics</h2>
                        @if(preg_match('/^G-[A-Z0-9]+$/', $site['settings']['ga4_id']))
                            <p>Uz zasebnu privolu Google Analytics 4 mjeri posjećenost i korištenje stranice. Pružatelj je Google Ireland Limited. Obrađuju se mrežni identifikatori, tehnički podaci o uređaju i podaci o pregledavanju. Kolačići _ga i _ga_&lt;ID&gt; na ovoj stranici traju najdulje 180 dana. Google Signals i personalizacija oglasa su isključeni. Bez privole ne učitavamo analitički kod niti šaljemo analitičke zahtjeve Googleu.</p>
                        @else
                            <p>Analitika trenutačno nije aktivna. Ne učitavamo Google Analytics niti postavljamo analitičke kolačiće. Ako se analitika uključi, zatražit ćemo zasebnu privolu prije početka mjerenja.</p>
                        @endif
                    </section>
                    <p class="provider-links"><a href="https://policies.google.com/privacy?hl=hr" target="_blank" rel="noopener noreferrer">Googleova pravila privatnosti</a> · <a href="https://policies.google.com/technologies/cookies?hl=hr" target="_blank" rel="noopener noreferrer">Googleova pravila o kolačićima</a></p>
                @endif
                @if($key === 'privacy')<a class="text-link" href="https://azop.hr/prava-ispitanika-2/" target="_blank" rel="noopener noreferrer">Više o pravima ispitanika na stranici AZOP-a</a>@endif
                @if($key === 'cookies')<button class="button" type="button" data-cookie-settings>Promijeni postavke kolačića</button>@endif
            @endif
        </article>
    </div>
</section>
@endsection
