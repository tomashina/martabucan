<?php
namespace App\Support;

final class SiteSettings
{
    public static function indexable(array $settings): bool
    {
        $host = request()->getHost();
        $local = in_array($host, ['localhost', '127.0.0.1', '::1']) || str_ends_with($host, '.test') || str_ends_with($host, '.localhost');
        return !$local && (bool) ($settings['indexable'] ?? false);
    }

    public static function baseUrl(array $settings): string
    {
        return rtrim($settings['site_url'] ?: request()->getSchemeAndHttpHost(), '/');
    }
}
