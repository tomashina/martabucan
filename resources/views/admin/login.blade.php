<!doctype html><html lang="hr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Prijava · Marta Bućan CMS</title>
@include('admin.partials.assets', ['editor' => false])
</head>
<body class="login-page"><main class="login-card"><x-brand /><p class="section-number">UPRAVLJANJE STRANICOM</p><h1>Dobro došli.</h1><p>Prijavite se za uređivanje sadržaja ordinacije.</p>
@if($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif
<form method="post" action="/admin/prijava">@csrf<label for="email">E-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus><label for="password">Lozinka</label><input id="password" name="password" type="password" required autocomplete="current-password"><button class="button" type="submit">Prijavi se </button></form><a class="login-back" href="/">Povratak na stranicu</a></main></body></html>
