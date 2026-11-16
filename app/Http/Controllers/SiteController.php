<?php
namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\SiteLocale;
use App\Support\PricePublication;
use Illuminate\View\View;

class SiteController
{
    public function home(): View { return view('home', ['site' => SiteContent::allSections(app()->getLocale())]); }
    public function pricing(): View
    {
        $site = SiteContent::allSections(app()->getLocale());
        return view('pricing', ['site' => $site, 'title' => $site['ui']['pricing_title'], 'priceVersion' => app(PricePublication::class)->current(app()->getLocale())]);
    }
    public function legal(string $page): View
    {
        $key = SiteLocale::page();
        abort_unless(in_array($key, ['privacy', 'cookies', 'terms', 'business']), 404);
        $site = SiteContent::allSections(app()->getLocale());
        return view('legal.page', ['site' => $site, 'key' => $key, 'title' => $site['page_meta'][$key]['title']]);
    }
}
