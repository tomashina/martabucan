<?php
namespace App\Support;

use Illuminate\Http\Request;

final class SiteLocale
{
    public const LANGUAGES = ['hr' => 'Hrvatski', 'de' => 'Deutsch'];

    public const PATHS = [
        'hr' => ['home' => '/', 'pricing' => '/cjenik', 'business' => '/podaci-o-ordinaciji', 'privacy' => '/privatnost', 'cookies' => '/kolacici', 'terms' => '/uvjeti-koristenja'],
        'de' => ['home' => '/de', 'pricing' => '/de/preise', 'business' => '/de/impressum', 'privacy' => '/de/datenschutz', 'cookies' => '/de/cookies', 'terms' => '/de/nutzungsbedingungen'],
    ];

    public static function url(string $page = 'home', ?string $locale = null): string
    {
        return self::PATHS[$locale ?? app()->getLocale()][$page] ?? '/';
    }

    public static function page(): string
    {
        $path = '/'.trim(request()->path(), '/');
        foreach (self::PATHS as $pages) {
            $page = array_search($path, $pages, true);
            if ($page !== false) return $page;
        }
        return 'home';
    }

    public static function cms(Request $request): string
    {
        $locale = $request->query('lang', 'hr');
        abort_unless(is_string($locale) && isset(self::LANGUAGES[$locale]), 400);
        return $locale;
    }

    public static function adminUrl(string $path, string $locale): string
    {
        return $path.($locale === 'hr' ? '' : '?lang='.$locale);
    }
}
