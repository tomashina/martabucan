<?php
namespace App\Providers;

use App\Models\SiteContent;
use App\Support\PricePublication;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        SiteContent::saved(function (SiteContent $content) {
            if (!$content->wasRecentlyCreated && !$content->wasChanged('value')) return;
            $locales = match ($content->key) {
                'prices' => ['hr'], 'de.prices' => ['de'], 'contact', 'business' => ['hr', 'de'], default => [],
            };
            if (!$locales || !Schema::hasTable('price_list_versions')) return;
            foreach ($locales as $locale) app(PricePublication::class)->publish($locale);
        });
    }
}
