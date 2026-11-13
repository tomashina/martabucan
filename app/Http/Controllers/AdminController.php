<?php
namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\SiteLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminController
{
    public const SECTIONS = ['intro' => 'Naslovnica', 'services' => 'Usluge', 'team' => 'Tim', 'prices' => 'Cjenik', 'contact' => 'Kontakt', 'business' => 'Podaci ordinacije', 'legal' => 'Pravni tekstovi'];

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (!Auth::attempt($credentials + ['is_admin' => true])) {
            throw ValidationException::withMessages(['email' => 'E-mail ili lozinka nisu ispravni.']);
        }
        $request->session()->regenerate();
        $request->session()->put('password_hash_web', Auth::guard('web')->hashPasswordForCookie($request->user()->getAuthPassword()));
        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect('/admin/prijava');
    }

    public function edit(Request $request, string $section = 'intro')
    {
        abort_unless($request->user()?->is_admin, 403);
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $mapping = ['intro'=>['home','hero'],'services'=>['home','services'],'team'=>['home','team'],'prices'=>['pricing','content'],'contact'=>['home','contact'],'business'=>['business','content'],'legal'=>['privacy','content']];
        return app(CmsController::class)->edit($request, ...$mapping[$section]);
    }

    public function update(Request $request, string $section)
    {
        abort_unless($request->user()?->is_admin, 403);
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $locale = SiteLocale::cms($request);
        $defaults = Arr::dot(config('content.'.$section));
        $rules = [];
        foreach ($defaults as $key => $default) {
            if (in_array(last(explode('.', $key)), ['icon', 'image'])) continue;
            $rules['content.'.$key] = str_ends_with($key, '.amount') ? ['nullable', 'numeric', 'min:0', 'max:100000']
                : (is_bool($default) ? ['required', 'boolean'] : ['nullable', 'string', 'max:15000']);
        }
        if ($section === 'contact') {
            $rules['content.email'] = ['required', 'email', 'max:254'];
            $rules['content.phone'] = ['required', 'regex:/^[+0-9 ()-]{5,30}$/'];
            $rules['content.address'] = ['required', 'string', 'max:255'];
            $rules['content.city'] = ['required', 'string', 'max:255'];
        }
        if ($section === 'business') $rules['content.oib'] = ['nullable', 'regex:/^\d{11}$/'];
        if ($section === 'prices') $rules['content.valid_from'] = ['nullable', 'date_format:Y-m-d'];
        $validated = $request->validate($rules);
        $content = SiteContent::section($section, $locale);
        foreach ($defaults as $key => $default) {
            if (!isset($rules['content.'.$key])) continue;
            $value = Arr::get($validated, 'content.'.$key);
            Arr::set($content, $key, is_bool($default) ? (bool) $value : ($value ?? (str_ends_with($key, '.amount') ? null : '')));
        }
        SiteContent::updateOrCreate(['key' => SiteContent::storageKey($section, $locale)], ['value' => $content]);
        return back()->with('saved', 'Promjene su spremljene i vidljive na stranici.');
    }
}
