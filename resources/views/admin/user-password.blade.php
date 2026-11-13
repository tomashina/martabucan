@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><a class="admin-breadcrumb" href="/admin/users">Svi korisnici</a><h1>{{ $ownAccount ? 'Moj račun' : 'Promjena lozinke' }}</h1><p>{{ $ownAccount ? 'Upravljajte lozinkom za prijavu u administraciju.' : 'Postavite novu lozinku za odabranog korisnika.' }}</p></div></header>
<form class="cms-editor-form account-form" action="{{ $ownAccount ? '/admin/account/password' : '/admin/users/'.$accountUser->id.'/password' }}" method="post">@csrf @method('put')
    <div class="account-identity"><span class="user-avatar">{{ mb_strtoupper(mb_substr($accountUser->name,0,1)) }}</span><div><h2>{{ $accountUser->name }} @if($accountUser->is_master)<span class="user-you">Master</span>@endif</h2><p>{{ $accountUser->email }}</p></div></div>
    <div class="editor-fields">
        <div class="field-wide account-current-password"><x-admin-password name="current_password" :label="$ownAccount ? 'Trenutačna lozinka' : 'Vaša master lozinka'" autocomplete="current-password" :help="$ownAccount ? null : 'Promjenu potvrdite svojom lozinkom, ne lozinkom ovog korisnika.'" /></div>
        <x-admin-password name="password" label="Nova lozinka" help="Najmanje 8 znakova." />
        <x-admin-password name="password_confirmation" label="Ponovite novu lozinku" />
    </div>
    <div class="editor-save"><span>{{ $ownAccount ? 'Mijenjate lozinku svog računa.' : 'Korisnik će se prijavljivati novom lozinkom.' }}</span><div><a class="admin-button ghost" href="/admin/users">Odustani</a><button class="admin-button" type="submit"><x-icon name="key" /> Spremi lozinku</button></div></div>
</form>
@endsection
