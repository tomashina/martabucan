@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><a class="admin-breadcrumb" href="/admin/users">Svi korisnici</a><h1>Obriši korisnika</h1><p>Korisnik će izgubiti pristup administraciji. Sadržaj web-stranice ostaje sačuvan.</p></div></header>
<form class="cms-editor-form account-form" action="/admin/users/{{ $accountUser->id }}" method="post">@csrf @method('delete')
    <div class="account-identity"><span class="user-avatar">{{ mb_strtoupper(mb_substr($accountUser->name,0,1)) }}</span><div><h2>{{ $accountUser->name }}</h2><p>{{ $accountUser->email }}</p></div></div>
    <div class="editor-fields"><div class="field-wide account-current-password"><x-admin-password name="current_password" label="Vaša master lozinka" autocomplete="current-password" help="Potvrdite trajno brisanje ovog korisnika svojom lozinkom." /></div></div>
    <div class="editor-save"><span>Brisanje računa nije moguće poništiti.</span><div><a class="admin-button secondary" href="/admin/users">Odustani</a><button class="admin-button danger" type="submit"><x-icon name="trash-can" /> Obriši korisnika</button></div></div>
</form>
@endsection
