<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PriceDownloadController;
use App\Http\Middleware\SetSiteLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetSiteLocale::class.':hr')->group(function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/cjenik', [SiteController::class, 'pricing'])->name('pricing');
    Route::get('/cjenik.xml', [PriceDownloadController::class, 'current'])->name('pricing.xml');
    Route::get('/cjenik/arhiva', [PriceDownloadController::class, 'archive'])->name('pricing.archive');
    Route::get('/{page}', [SiteController::class, 'legal'])->where('page', 'privatnost|kolacici|uvjeti-koristenja|podaci-o-ordinaciji');
});
Route::middleware(SetSiteLocale::class.':de')->prefix('de')->name('de.')->group(function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/preise', [SiteController::class, 'pricing'])->name('pricing');
    Route::get('/preise.xml', [PriceDownloadController::class, 'current'])->name('pricing.xml');
    Route::get('/preise/archiv', [PriceDownloadController::class, 'archive'])->name('pricing.archive');
    Route::get('/{page}', [SiteController::class, 'legal'])->where('page', 'datenschutz|cookies|nutzungsbedingungen|impressum');
});
Route::redirect('/index.html', '/', 301);
Route::get('/cjenik/datoteke/{version}.xml', [PriceDownloadController::class, 'download'])->whereNumber('version')->name('pricing.download');
Route::redirect('/de.html', '/de', 301);
Route::get('/robots.txt', function () {
    $settings = \App\Models\SiteContent::allSections()['settings'];
    $body = \App\Support\SiteSettings::indexable($settings) ? "User-agent: *\nDisallow: /admin\n" : "User-agent: *\nDisallow: /\n";
    $body .= "Allow: /cjenik.xml\nAllow: /de/preise.xml\nAllow: /cjenik/datoteke/\nAllow: /cjenik/arhiva\nAllow: /de/preise/archiv\n";
    return response($body, 200, ['Content-Type'=>'text/plain']);
});
Route::view('/admin/prijava', 'admin.login')->middleware('guest')->name('login');
Route::post('/admin/prijava', [AdminController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::middleware(['auth', 'auth.session'])->prefix('admin')->group(function () {
    Route::post('/odjava', [AdminController::class, 'logout'])->name('logout');
    Route::get('/', [CmsController::class, 'index']);
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/create', [AdminUserController::class, 'create']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::get('/account', [AdminUserController::class, 'account']);
    Route::put('/account/password', [AdminUserController::class, 'updateOwnPassword'])->middleware('throttle:5,1');
    Route::get('/users/{user}/password', [AdminUserController::class, 'editPassword']);
    Route::put('/users/{user}/password', [AdminUserController::class, 'updatePassword'])->middleware('throttle:5,1');
    Route::get('/users/{user}/delete', [AdminUserController::class, 'confirmDelete']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('throttle:5,1');
    Route::get('/pages/{page}/{block?}', [CmsController::class, 'edit']);
    Route::put('/pages/{page}/{block}', [CmsController::class, 'update']);
    Route::get('/{section}', [AdminController::class, 'edit']);
    Route::put('/{section}', [AdminController::class, 'update']);
});
