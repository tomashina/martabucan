@extends('layouts.site')
@section('body-class', 'interior-page')
@push('styles')<link rel="stylesheet" href="/assets/css/pages.css?v={{ substr(hash_file('sha256', public_path('assets/css/pages.css')), 0, 16) }}">@endpush
@section('content')
<x-page-heading :eyebrow="$site['prices']['eyebrow']">
    <x-slot:heading>{{ $site['prices']['title'] }} <span class="serif-accent">{{ $site['prices']['accent'] }}</span></x-slot:heading>
    <x-rich-text :text="$site['prices']['note']" />
</x-page-heading>
<section class="section pricing-page" aria-label="Usluge i cijene">
    <div class="container pricing-page-grid">
        <aside class="pricing-intro-card">
            <x-icon name="tooth" />
            <h2>{{ $site['prices']['aside_title'] }}</h2>
            <x-rich-text :text="$site['prices']['aside_text']" />
            <a class="button" href="/#kontakt">Dogovorite pregled</a>
            <div class="pricing-payment">
                <x-icon name="credit-card" />
                <h3>Mogućnosti plaćanja</h3>
                <p>{{ $site['contact']['payment_note'] }}</p>
                <p class="payment-methods">Visa · Mastercard · American Express · Gotovina</p>
            </div>
        </aside>
        <div class="price-list">
            <div class="price-list-heading"><span>Usluga</span><span>Cijena / EUR</span></div>
            @foreach($site['prices']['items'] as $price)
                <div class="price-row">
                    <div><h3>{{ $price['name'] }}</h3><p>{{ $price['detail'] }}</p></div>
                    <span class="price-amount">@if($price['amount'] !== null && $price['amount'] !== '')@if(isset($price['anchor_amount']) && $price['anchor_amount'] > $price['amount'])<del class="price-anchor"><span class="price-sr-only">Referentna cijena: </span>{{ number_format((float) $price['anchor_amount'], 2, ',', '.') }} €</del>@endif<span class="price-current">@if($price['from'])<small>od </small>@endif{{ number_format((float) $price['amount'], 2, ',', '.') }} €</span>@else<span class="price-pending">Na upit</span>@endif</span>
                </div>
            @endforeach
            @if($site['prices']['tax_note'] || $site['prices']['valid_from'])
                <p class="price-footnote">{{ $site['prices']['tax_note'] }}@if($site['prices']['valid_from']) Cjenik vrijedi od {{ \Carbon\Carbon::parse($site['prices']['valid_from'])->format('d. m. Y.') }}@endif</p>
            @endif
            <p class="price-footnote">Konačna cijena ovisi o opsegu terapije i dogovara se nakon pregleda, prije početka zahvata. Oznaka „od” predstavlja početnu cijenu navedene usluge.</p>
            <a class="text-link" href="mailto:{{ $site['contact']['email'] }}?subject=Upit%20za%20cijenu%20usluge">Zatražite individualnu ponudu</a>
        </div>
    </div>
</section>
@endsection
