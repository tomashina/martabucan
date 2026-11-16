@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><a class="admin-breadcrumb" href="{{ \App\Support\SiteLocale::adminUrl('/admin', $locale) }}">Sve stranice</a><h1>{{ $page['label'] }}</h1><p>{{ $page['description'] }}</p></div><a class="admin-button secondary" href="{{ $page['url'].($block['anchor'] ? '#'.$block['anchor'] : '') }}" target="_blank" rel="noopener">Pogledaj na stranici <x-icon name="up-right-from-square" /></a></header>
@if(count($page['blocks']) > 1)<nav class="admin-block-nav" aria-label="Sekcije stranice">@foreach($page['blocks'] as $key=>$item)<a href="/admin/pages/{{ $pageKey }}/{{ $key }}{{ $locale === 'de' ? '?lang=de' : '' }}" @if($blockKey===$key) aria-current="page" @endif>{{ $item['label'] }}</a>@endforeach</nav>@endif
@if($pageKey === 'settings')<p class="admin-language-note">SEO i tekstovi za dijeljenje uređuju se po jeziku. Domena, indeksiranje, Google i društvene poveznice zajednički su za oba jezika.</p>@endif
@if($pageKey === 'pricing')
<p class="admin-language-note">Sidrena cijena prikazuje se uz aktualnu, bez precrtavanja, i kada je jednaka ili niža. Za postojeće usluge provjerite iznose na 10. 9. 2026.; datum važenja aktualnog cjenika nije datum sidrenja. Novi XML nastaje automatski pri spremanju promijenjenih cijena, datuma ili podataka objekta. Prethodne verzije ostaju u <a href="{{ route($locale === 'de' ? 'de.pricing.archive' : 'pricing.archive') }}" target="_blank" rel="noopener">arhivi cjenika</a>. Za XML svaka stavka mora imati aktualnu i sidrenu cijenu te referentni datum.</p>
@endif
<form class="cms-editor-form" action="/admin/pages/{{ $pageKey }}/{{ $blockKey }}{{ $locale === 'de' ? '?lang=de' : '' }}" method="post" enctype="multipart/form-data" data-editor-form>@csrf @method('PUT')
<input type="hidden" name="revision" value="{{ old('revision',$revision) }}">
<div class="editor-section-heading"><div><h2>{{ $block['label'] }}</h2><p>{{ $block['description'] }}</p></div><span class="editor-status" data-editor-status role="status">Sve promjene su spremljene</span></div>
<div class="editor-fields">
@foreach($block['fields'] as $fieldKey=>$field)
    @include('admin.partials.field',['field'=>$field,'path'=>'fields.'.$fieldKey,'value'=>old('fields.'.$fieldKey,$values[$fieldKey] ?? ''),'template'=>false])
@endforeach
</div>
<div class="editor-save"><span data-save-note>Promjene postaju vidljive nakon spremanja.</span><div><a class="admin-button ghost" href="/admin/pages/{{ $pageKey }}/{{ $blockKey }}{{ $locale === 'de' ? '?lang=de' : '' }}" data-discard>Odustani</a><button class="admin-button" type="submit" data-save-button><x-icon name="check" /> Spremi promjene</button></div></div>
</form>
@endsection
