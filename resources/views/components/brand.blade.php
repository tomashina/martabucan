@props(['site' => null])
@php $brandUi = $site['ui'] ?? \App\Models\SiteContent::defaults()['ui']; @endphp
<a class="brand" href="{{ \App\Support\SiteLocale::url('home', $site ? app()->getLocale() : 'hr') }}" aria-label="{{ $brandUi['brand_label'] }}">
    <span class="brand-mark"><x-icon name="tooth" /></span>
    <span class="brand-name">
        <span class="brand-wordmark">marta bućan</span>
        <span class="brand-tagline"><span>{{ $brandUi['brand_tagline_a'] }}</span> <span>{{ $brandUi['brand_tagline_b'] }}</span> <span>{{ $brandUi['brand_tagline_c'] }}</span></span>
    </span>
</a>
