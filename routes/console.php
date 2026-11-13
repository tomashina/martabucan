<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

Artisan::command('cms:create-admin {--master : Kreiraj zaštićenog master administratora}', function () {
    $name = $this->ask('Ime administratora');
    $email = $this->ask('E-mail');
    $password = $this->secret('Lozinka (najmanje 12 znakova)');
    $validation = Validator::make(compact('name', 'email', 'password'), [
        'name' => 'required|string|max:100', 'email' => 'required|email|unique:users,email', 'password' => 'required|string|min:12',
    ]);
    if ($validation->fails()) { foreach ($validation->errors()->all() as $error) $this->error($error); return 1; }
    $user = new User(compact('name', 'email', 'password') + ['is_admin' => true]);
    $user->is_master = (bool) $this->option('master');
    $user->save();
    $this->info('Administrator je kreiran. Prijava: '.url('/admin/prijava'));
})->purpose('Sigurno kreiranje CMS administratora bez zadane lozinke');
