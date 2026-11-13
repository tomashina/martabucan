@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><p class="admin-kicker">SADRŽAJ STRANICE</p><h1>Vaše stranice</h1><p>Odaberite stranicu koju želite urediti.</p></div><a class="admin-button secondary" href="/" target="_blank" rel="noopener">Pogledaj web-stranicu <x-icon name="up-right-from-square" /></a></header>
<div class="admin-page-grid">@foreach($pages as $key=>$page)
<article class="admin-page-card"><span class="page-card-icon"><x-icon :name="$page['icon']" /></span><h2>{{ $page['label'] }}</h2><p>{{ $page['description'] }}</p><div class="page-card-footer"><span>{{ count($page['blocks']) === 1 ? 'Jednostavno uređivanje' : count($page['blocks']).' sekcija' }}</span><a href="/admin/pages/{{ $key }}">Uredi <span class="sr-only">{{ $page['label'] }}</span><x-icon name="pen" /></a></div></article>
@endforeach
<article class="admin-page-card"><span class="page-card-icon"><x-icon name="users" /></span><h2>Korisnici</h2><p>{{ auth()->user()->is_master ? 'Dodavanje administratora, promjena lozinki i upravljanje pristupom.' : 'Pregled administratora i promjena lozinke vašeg računa.' }}</p><div class="page-card-footer"><span>Upravljanje pristupom</span><a href="/admin/users">Otvori korisnike <x-icon name="users" /></a></div></article>
</div>
@endsection
