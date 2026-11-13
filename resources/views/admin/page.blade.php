@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><a class="admin-breadcrumb" href="/admin">Sve stranice</a><h1>{{ $page['label'] }}</h1><p>{{ $page['description'] }}</p></div><a class="admin-button secondary" href="{{ $page['url'].($block['anchor'] ? '#'.$block['anchor'] : '') }}" target="_blank" rel="noopener">Pogledaj na stranici <x-icon name="up-right-from-square" /></a></header>
@if(count($page['blocks']) > 1)<nav class="admin-block-nav" aria-label="Sekcije stranice">@foreach($page['blocks'] as $key=>$item)<a href="/admin/pages/{{ $pageKey }}/{{ $key }}" @if($blockKey===$key) aria-current="page" @endif>{{ $item['label'] }}</a>@endforeach</nav>@endif
<form class="cms-editor-form" action="/admin/pages/{{ $pageKey }}/{{ $blockKey }}" method="post" enctype="multipart/form-data" data-editor-form>@csrf @method('PUT')
<input type="hidden" name="revision" value="{{ old('revision',$revision) }}">
<div class="editor-section-heading"><div><h2>{{ $block['label'] }}</h2><p>{{ $block['description'] }}</p></div><span class="editor-status" data-editor-status role="status">Sve promjene su spremljene</span></div>
<div class="editor-fields">
@foreach($block['fields'] as $fieldKey=>$field)
    @include('admin.partials.field',['field'=>$field,'path'=>'fields.'.$fieldKey,'value'=>old('fields.'.$fieldKey,$values[$fieldKey] ?? ''),'template'=>false])
@endforeach
</div>
<div class="editor-save"><span data-save-note>Promjene postaju vidljive nakon spremanja.</span><div><a class="admin-button ghost" href="/admin/pages/{{ $pageKey }}/{{ $blockKey }}" data-discard>Odustani</a><button class="admin-button" type="submit" data-save-button><x-icon name="check" /> Spremi promjene</button></div></div>
</form>
@endsection
