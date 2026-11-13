<?php
namespace App\Http\Middleware;

use App\Support\SiteLocale;
use Closure;
use Illuminate\Http\Request;

class SetSiteLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'hr')
    {
        abort_unless(isset(SiteLocale::LANGUAGES[$locale]), 404);
        app()->setLocale($locale);
        return $next($request);
    }
}
