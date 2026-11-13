@extends('admin.layout')
@section('admin-content')
<header class="admin-heading"><div><a class="admin-breadcrumb" href="/admin/users">Svi korisnici</a><h1>Novi administrator</h1><p>Upišite podatke za prijavu novog administratora.</p></div></header>
<form class="cms-editor-form" action="/admin/users" method="post" data-user-form>@csrf
    <div class="editor-section-heading"><div><h2>Podaci za pristup</h2><p>Administrator može uređivati sve stranice, cijene, fotografije i postavke. Korisnicima upravlja master račun.</p></div></div>
    <div class="editor-fields">
        <div class="editor-field"><label for="user-name">Ime i prezime <span class="required-mark">*</span></label><input id="user-name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="editor-field"><label for="user-email">E-mail za prijavu <span class="required-mark">*</span></label><input id="user-email" name="email" value="{{ old('email') }}" type="email" autocomplete="off" maxlength="254" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="editor-field"><label for="user-password">Lozinka <span class="required-mark">*</span></label><div class="password-control"><input id="user-password" name="password" type="password" autocomplete="new-password" minlength="8" maxlength="255" required><button type="button" data-toggle-password="user-password" aria-label="Prikaži lozinku" aria-pressed="false"><x-icon name="eye" /></button></div><p class="field-help">Najmanje 8 znakova.</p>@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="editor-field"><label for="user-password-confirmation">Ponovite lozinku <span class="required-mark">*</span></label><input id="user-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="255" required></div>
    </div>
    <div class="editor-save"><span>Novi račun imat će administratorski pristup.</span><div><a class="admin-button ghost" href="/admin/users">Odustani</a><button class="admin-button" type="submit"><x-icon name="user-plus" /> Dodaj administratora</button></div></div>
</form>
@endsection
