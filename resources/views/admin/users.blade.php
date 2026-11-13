@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><p class="admin-kicker">ADMINISTRACIJA</p><h1>Korisnici</h1><p>Administratori koji mogu uređivati sadržaj i postavke stranice.</p></div>@if(auth()->user()->is_master)<a class="admin-button" href="/admin/users/create"><x-icon name="user-plus" /> Dodaj administratora</a>@endif</header>
<section class="admin-users-panel" aria-label="Administratori">
    <div class="admin-users-heading"><h2>Administratori</h2><span>{{ $users->total() }} {{ $users->total() === 1 ? 'korisnik' : 'korisnika' }}</span></div>
    @foreach($users as $user)
    <article class="admin-user-row">
        <span class="user-avatar">{{ mb_strtoupper(mb_substr($user->name,0,1)) }}</span>
        <div><h3>{{ $user->name }} @if($user->id===auth()->id())<span class="user-you">Vi</span>@endif</h3><p>{{ $user->email }}</p></div>
        <div class="user-row-actions">
            <span class="user-role"><x-icon :name="$user->is_master ? 'lock-keyhole' : 'shield-check'" /> {{ $user->is_master ? 'Master · zaštićen' : 'Administrator' }}</span>
            @if($user->id===auth()->id())
                <a class="admin-button secondary" href="/admin/account"><x-icon name="key" /> Promijeni lozinku</a>
            @elseif(auth()->user()->is_master && !$user->is_master)
                <a class="admin-button secondary" href="/admin/users/{{ $user->id }}/password" aria-label="Promijeni lozinku: {{ $user->name }}"><x-icon name="key" /> Lozinka</a>
                <a class="admin-button danger-outline" href="/admin/users/{{ $user->id }}/delete" aria-label="Obriši korisnika: {{ $user->name }}"><x-icon name="trash-can" /> Obriši</a>
            @endif
        </div>
    </article>
    @endforeach
    @if($users->hasPages())<nav class="admin-pagination" aria-label="Stranice korisnika">@if($users->previousPageUrl())<a class="admin-button secondary" href="{{ $users->previousPageUrl() }}">Prethodna</a>@endif<span>{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>@if($users->nextPageUrl())<a class="admin-button secondary" href="{{ $users->nextPageUrl() }}">Sljedeća</a>@endif</nav>@endif
</section>
@endsection
