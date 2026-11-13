<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminUserController
{
    private function authorize(Request $request): void { abort_unless($request->user()?->is_admin, 403); }

    private function authorizeMaster(Request $request, ?User $target = null): void
    {
        $this->authorize($request);
        abort_unless($request->user()->is_master, 403);
        if ($target) abort_if($target->is_master || $target->is($request->user()) || !$target->is_admin, 403);
    }

    public function index(Request $request)
    {
        $this->authorize($request);
        return view('admin.users', ['pages'=>config('cms.pages'),'pageKey'=>'users','title'=>'Korisnici','users'=>User::where('is_admin',true)->orderBy('name')->paginate(20)]);
    }

    public function create(Request $request)
    {
        $this->authorizeMaster($request);
        return view('admin.user-create', ['pages'=>config('cms.pages'),'pageKey'=>'users','title'=>'Novi administrator']);
    }

    public function store(Request $request)
    {
        $this->authorizeMaster($request);
        if (is_string($request->input('email'))) $request->merge(['email'=>Str::lower(trim($request->input('email')))]);
        $values = $request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','email','max:254',function ($attribute,$value,$fail) {
                if (User::whereRaw('LOWER(email) = ?', [$value])->exists()) $fail('Korisnik s ovom e-mail adresom već postoji.');
            }],
            'password'=>['required','string',Password::min(8),'max:255','confirmed'],
        ], ['required'=>'Polje :attribute je obavezno.','email'=>'Upišite ispravnu e-mail adresu.','password.min'=>'Lozinka mora imati najmanje 8 znakova.','min'=>'Lozinka mora imati najmanje 8 znakova.','confirmed'=>'Lozinke se ne podudaraju.'], ['name'=>'Ime i prezime','email'=>'E-mail','password'=>'Lozinka']);
        User::create($values + ['is_admin'=>true]);
        return redirect('/admin/users')->with('saved','Novi administrator je dodan i može se prijaviti svojim e-mailom i lozinkom.');
    }

    public function account(Request $request)
    {
        $this->authorize($request);
        return $this->passwordView($request->user(), true);
    }

    public function editPassword(Request $request, User $user)
    {
        $this->authorizeMaster($request, $user);
        return $this->passwordView($user, false);
    }

    private function passwordView(User $user, bool $ownAccount)
    {
        return view('admin.user-password', [
            'pages'=>config('cms.pages'), 'pageKey'=>$ownAccount ? 'account' : 'users',
            'title'=>$ownAccount ? 'Moj račun' : 'Promjena lozinke', 'accountUser'=>$user, 'ownAccount'=>$ownAccount,
        ]);
    }

    public function updateOwnPassword(Request $request)
    {
        $this->authorize($request);
        $this->savePassword($request, $request->user());
        $request->session()->regenerate(true);
        return redirect('/admin/account')->with('saved', 'Vaša lozinka je promijenjena.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->authorizeMaster($request, $user);
        $this->savePassword($request, $user);
        return redirect('/admin/users')->with('saved', 'Lozinka za korisnika '.$user->name.' je promijenjena.');
    }

    private function savePassword(Request $request, User $user): void
    {
        $values = $request->validate([
            'current_password'=>['required', 'string', 'current_password:web'],
            'password'=>['required', 'string', Password::min(8), 'max:255', 'confirmed'],
        ], $this->passwordMessages(), [
            'current_password'=>'Trenutačna lozinka', 'password'=>'Nova lozinka',
        ]);
        if (Hash::check($values['password'], $user->password)) {
            throw ValidationException::withMessages(['password'=>'Nova lozinka mora biti različita od dosadašnje.']);
        }
        $user->forceFill(['password'=>$values['password'], 'remember_token'=>Str::random(60)])->save();
    }

    public function confirmDelete(Request $request, User $user)
    {
        $this->authorizeMaster($request, $user);
        return view('admin.user-delete', ['pages'=>config('cms.pages'), 'pageKey'=>'users', 'title'=>'Brisanje korisnika', 'accountUser'=>$user]);
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeMaster($request, $user);
        $request->validate(['current_password'=>['required', 'string', 'current_password:web']], $this->passwordMessages(), ['current_password'=>'Vaša lozinka']);
        $name = $user->name;
        $user->delete();
        return redirect('/admin/users')->with('saved', 'Korisnik '.$name.' je obrisan.');
    }

    private function passwordMessages(): array
    {
        return [
            'required'=>'Polje :attribute je obavezno.',
            'current_password'=>'Vaša trenutačna lozinka nije ispravna.',
            'password.min'=>'Nova lozinka mora imati najmanje 8 znakova.',
            'password.max'=>'Nova lozinka može imati najviše 255 znakova.',
            'confirmed'=>'Lozinke se ne podudaraju.',
        ];
    }
}
