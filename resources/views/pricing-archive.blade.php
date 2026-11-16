@extends('layouts.site')
@section('body-class', 'interior-page')
@push('styles')<link rel="stylesheet" href="/assets/css/pages.css?v={{ substr(hash_file('sha256', public_path('assets/css/pages.css')), 0, 16) }}">@endpush
@section('content')
<x-page-heading :site="$site" :eyebrow="$site['prices']['eyebrow']">
    <x-slot:heading>{{ $site['ui']['price_archive'] }}</x-slot:heading>
    <p>{{ $site['ui']['price_archive_intro'] }}</p>
</x-page-heading>
<section class="section">
    <div class="container">
        <a class="text-link" href="{{ \App\Support\SiteLocale::url('pricing') }}">{{ $site['ui']['price_back'] }}</a>
        <ul class="price-archive-list">
            @forelse($versions as $version)
                <li><a href="{{ route('pricing.download', $version) }}"><span>{{ $version->published_at->timezone('Europe/Zagreb')->format('d. m. Y. H:i') }} · #{{ $version->id }}</span><span>{{ $version->filename() }}</span></a></li>
            @empty<li>{{ $site['ui']['price_archive_empty'] }}</li>@endforelse
        </ul>
        <nav class="price-downloads" aria-label="{{ $site['ui']['price_archive'] }}">
            @if($versions->previousPageUrl())<a class="text-link" href="{{ $versions->previousPageUrl() }}">{{ $site['ui']['price_newer'] }}</a>@endif
            @if($versions->nextPageUrl())<a class="text-link" href="{{ $versions->nextPageUrl() }}">{{ $site['ui']['price_older'] }}</a>@endif
        </nav>
    </div>
</section>
@endsection
