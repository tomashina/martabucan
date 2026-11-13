<div class="admin-language-bar">
    <div><span>Jezik sadržaja</span><strong>{{ $locale === 'de' ? 'Njemački · Deutsch' : 'Hrvatski' }}</strong></div>
    <nav aria-label="Odaberi jezik sadržaja">
        @foreach(\App\Support\SiteLocale::LANGUAGES as $language => $label)
            <a href="{{ \App\Support\SiteLocale::adminUrl(request()->getPathInfo(), $language) }}" @if($locale === $language) aria-current="true" @endif>{{ strtoupper($language) }} <span>{{ $label }}</span></a>
        @endforeach
    </nav>
</div>
