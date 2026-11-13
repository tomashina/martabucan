<?php
namespace App\Http\Controllers;

use App\Models\SiteContent;
use Illuminate\View\View;

class SiteController
{
    public function home(): View { return view('home', ['site' => SiteContent::allSections()]); }
    public function pricing(): View { return view('pricing', ['site' => SiteContent::allSections(), 'title' => 'Cjenik usluga']); }
    public function legal(string $page): View
    {
        $pages = ['privatnost' => ['privacy', 'Politika privatnosti'], 'kolacici' => ['cookies', 'Politika kolačića'], 'uvjeti-koristenja' => ['terms', 'Uvjeti korištenja'], 'podaci-o-ordinaciji' => ['business', 'Podaci o ordinaciji']];
        abort_unless(isset($pages[$page]), 404);
        [$key, $title] = $pages[$page];
        $site = SiteContent::allSections();
        return view('legal.page', ['site' => $site, 'key' => $key, 'title' => $site['page_meta'][$key]['title']]);
    }
}
