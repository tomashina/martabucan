<!doctype html>
<html lang="hr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>{{ $title }} · Marta Bućan CMS</title>
@include('admin.partials.assets', ['editor' => true])
</head><body class="admin-body">
<aside class="admin-sidebar" id="cms-sidebar">
    <x-brand />
    <a class="admin-overview {{ !$pageKey ? 'selected' : '' }}" href="/admin"><x-icon name="grid-2" /> Pregled stranica</a>
    <p class="admin-nav-label">STRANICE</p>
    <nav aria-label="Stranice u CMS-u">@foreach($pages as $key=>$navPage)@continue($key==='settings')<a href="/admin/pages/{{ $key }}" @class(['selected'=>$pageKey===$key]) @if($pageKey===$key) aria-current="page" @endif><x-icon :name="$navPage['icon']" /><span>{{ $navPage['label'] }}</span></a>@endforeach</nav>
    <p class="admin-nav-label">ADMINISTRACIJA</p>
    <nav aria-label="Postavke administracije"><a href="/admin/pages/settings" @class(['selected'=>$pageKey==='settings']) @if($pageKey==='settings') aria-current="page" @endif><x-icon name="gear" /><span>Postavke</span></a><a href="/admin/users" @class(['selected'=>$pageKey==='users']) @if($pageKey==='users') aria-current="page" @endif><x-icon name="users" /><span>Korisnici</span></a><a href="/admin/account" @class(['selected'=>$pageKey==='account']) @if($pageKey==='account') aria-current="page" @endif><x-icon name="user-gear" /><span>Moj račun</span></a></nav>
    <div class="admin-sidebar-bottom"><a href="/" target="_blank" rel="noopener"><x-icon name="up-right-from-square" /> Otvori web-stranicu</a><form method="post" action="/admin/odjava">@csrf<button type="submit"><x-icon name="arrow-right-from-bracket" /> Odjavi se</button></form></div>
</aside>
<div class="admin-workspace">
    <header class="admin-topbar"><button class="admin-menu" type="button" aria-label="Otvori CMS navigaciju" aria-expanded="false" aria-controls="cms-sidebar"><x-icon name="bars" /></button><span>Uređivanje web-stranice</span><a class="admin-user" href="/admin/account" aria-label="Moj račun: {{ auth()->user()->name }}"><span>{{ mb_substr(auth()->user()->name,0,1) }}</span>{{ auth()->user()->name }} <x-icon name="angle-down" /></a></header>
    <main class="admin-main">
        @if(session('saved'))<div class="form-success" role="status"><x-icon name="circle-check" /> {{ session('saved') }}</div>@endif
        @if($errors->any())<div class="form-errors" role="alert"><strong>Provjerite označena polja.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@if($errors->has('uploads.*'))<p>Fotografije odaberite ponovno prije spremanja.</p>@endif</div>@endif
        @yield('admin-content')
    </main>
</div>
<div class="admin-toast" role="status" data-undo-toast hidden><span>Stavka je uklonjena.</span><button type="button" data-undo-remove>Vrati</button></div>
</body></html>
