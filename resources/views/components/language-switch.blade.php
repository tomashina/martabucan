@props(['site'])
<nav class="language-switch" aria-label="{{ $site['ui']['language_label'] }}">
    @foreach(\App\Support\SiteLocale::LANGUAGES as $language => $label)
        <a href="{{ \App\Support\SiteLocale::url(\App\Support\SiteLocale::page(), $language) }}" lang="{{ $language }}" hreflang="{{ $language }}" aria-label="{{ $label }}" @if(app()->getLocale() === $language) aria-current="true" @endif @if(\App\Support\SiteLocale::page() === 'home') data-preserve-anchor @endif>{{ strtoupper($language) }}</a>
    @endforeach
</nav>
