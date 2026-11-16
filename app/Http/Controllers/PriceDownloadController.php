<?php

namespace App\Http\Controllers;

use App\Models\PriceListVersion;
use App\Models\SiteContent;
use App\Support\PricePublication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PriceDownloadController
{
    public function current(PricePublication $publication): RedirectResponse
    {
        $version = $publication->current(app()->getLocale());
        abort_unless($version, 503, 'Cjenik za preuzimanje još nije potpun.');
        return redirect()->route('pricing.download', $version)->header('Cache-Control', 'no-store');
    }

    public function download(PriceListVersion $version): Response
    {
        return response($version->xml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$version->filename().'"',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function archive(): View
    {
        $site = SiteContent::allSections(app()->getLocale());
        return view('pricing-archive', [
            'site' => $site, 'title' => $site['ui']['price_archive'],
            'versions' => PriceListVersion::where('locale', app()->getLocale())->latest('id')->paginate(20),
        ]);
    }
}
