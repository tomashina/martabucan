@extends('layouts.site')
@section('body-class', 'interior-page')
@push('styles')<link rel="stylesheet" href="/assets/css/pages.css?v={{ substr(hash_file('sha256', public_path('assets/css/pages.css')), 0, 16) }}">@endpush
@section('content')
<x-page-heading :site="$site" :eyebrow="$site['prices']['eyebrow']">
    <x-slot:heading>{{ $site['prices']['title'] }} <span class="serif-accent">{{ $site['prices']['accent'] }}</span></x-slot:heading>
    <x-rich-text :text="$site['prices']['note']" />
</x-page-heading>
<section class="section pricing-page" aria-label="{{ $site['ui']['pricing_label'] }}">
    <div class="container pricing-page-grid">
        <aside class="pricing-intro-card">
            <x-icon name="tooth" />
            <h2>{{ $site['prices']['aside_title'] }}</h2>
            <x-rich-text :text="$site['prices']['aside_text']" />
            <a class="button" href="{{ \App\Support\SiteLocale::url('home') }}#kontakt">{{ $site['ui']['book_exam'] }}</a>
            <div class="pricing-payment">
                <x-icon name="credit-card" />
                <h3>{{ $site['ui']['payments_title'] }}</h3>
                <p>{{ $site['contact']['payment_note'] }}</p>
                <p class="payment-methods">{{ $site['ui']['payment_methods'] }}</p>
            </div>
        </aside>
        <div class="price-list">
            <div class="price-list-heading"><span>{{ $site['ui']['price_service'] }}</span><span>{{ $site['ui']['price_amount'] }}</span></div>
            @foreach($site['prices']['items'] as $price)
                <div class="price-row">
                    <div><h3>{{ $price['name'] }}</h3><p>{{ $price['detail'] }}</p></div>
                    <span class="price-amount">@if($price['amount'] !== null && $price['amount'] !== '')@if(isset($price['anchor_amount']) && $price['anchor_amount'] > $price['amount'])<del class="price-anchor"><span class="price-sr-only">{{ $site['ui']['reference_price'] }} </span>{{ number_format((float) $price['anchor_amount'], 2, ',', '.') }} €</del>@endif<span class="price-current">@if($price['from'])<small>{{ $site['ui']['price_from'] }} </small>@endif{{ number_format((float) $price['amount'], 2, ',', '.') }} €</span>@else<span class="price-pending">{{ $site['ui']['price_on_request'] }}</span>@endif</span>
                </div>
            @endforeach
            @if($site['prices']['tax_note'] || $site['prices']['valid_from'])
                <p class="price-footnote">{{ $site['prices']['tax_note'] }}@if($site['prices']['valid_from']) {{ $site['ui']['price_valid_from'] }} {{ \Carbon\Carbon::parse($site['prices']['valid_from'])->format('d. m. Y.') }}@endif</p>
            @endif
            <p class="price-footnote">{{ $site['ui']['price_disclaimer'] }}</p>
            <a class="text-link" href="mailto:{{ $site['contact']['email'] }}?subject={{ rawurlencode($site['ui']['price_email_subject']) }}">{{ $site['ui']['price_quote'] }}</a>
        </div>
    </div>
</section>
@endsection
