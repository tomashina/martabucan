<?php
namespace App\Models;

use App\Support\RichText;
use App\Support\SiteLocale;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    public const TRANSLATED_SETTINGS = ['site_name', 'meta_title', 'meta_description', 'og_title', 'og_description', 'og_image', 'og_image_alt'];
    protected $fillable = ['key', 'value'];
    protected function casts(): array { return ['value' => 'array']; }

    public static function merge(array $defaults, array $stored): array
    {
        // Lists are replaced in full: removed rows must never reappear from defaults.
        if (array_is_list($defaults)) return $stored;
        foreach ($stored as $key => $value) {
            $defaults[$key] = is_array($value) && isset($defaults[$key]) && is_array($defaults[$key])
                ? static::merge($defaults[$key], $value) : $value;
        }
        return $defaults;
    }

    public static function defaults(string $locale = 'hr'): array
    {
        abort_unless(isset(SiteLocale::LANGUAGES[$locale]), 400);
        $defaults = static::merge(config('content'), config('cms_defaults', []));
        if ($locale === 'de') $defaults = static::merge($defaults, config('content_de'));
        foreach (config('interface') as $group) {
            foreach ($group['fields'] as $key => $field) $defaults['ui'][$key] = $field[$locale];
        }
        return $defaults;
    }

    public static function storageKey(string $section, string $locale = 'hr'): string
    {
        abort_unless(isset(SiteLocale::LANGUAGES[$locale]), 400);
        return $locale === 'hr' ? $section : $locale.'.'.$section;
    }

    public static function sharedField(string $section, ?string $path): bool
    {
        return $section === 'settings' && !in_array($path, static::TRANSLATED_SETTINGS, true);
    }

    public static function section(string $key, string $locale = 'hr'): array
    {
        $defaults = static::defaults($locale)[$key] ?? [];
        $stored = static::where('key', static::storageKey($key, $locale))->first();
        $value = $stored ? static::merge($defaults, $stored->value) : $defaults;
        if ($key === 'settings' && $locale !== 'hr') {
            foreach (static::section('settings') as $field => $shared) {
                if (static::sharedField('settings', $field)) $value[$field] = $shared;
            }
        }
        return $value;
    }

    public static function allSections(string $locale = 'hr'): array
    {
        $stored = static::all()->pluck('value', 'key');
        $data = collect(static::defaults($locale))->map(function ($value, $key) use ($stored, $locale) {
            $storageKey = static::storageKey($key, $locale);
            return $stored->has($storageKey) ? static::merge($value, $stored[$storageKey]) : $value;
        })->all();
        if ($locale !== 'hr') {
            $shared = static::merge(static::defaults()['settings'], $stored['settings'] ?? []);
            foreach ($shared as $field => $value) {
                if (static::sharedField('settings', $field)) $data['settings'][$field] = $value;
            }
        }
        $data['intro']['about_html'] ??= RichText::html($data['intro']['about_text']).RichText::html($data['intro']['about_detail']);
        $data['intro']['results_html'] ??= RichText::html($data['intro']['results_intro']).RichText::html($data['intro']['results_text']);
        $data['settings']['ga4_id'] ??= config('integrations.ga4_measurement_id', '');
        $data['settings']['maps_url'] ??= config('integrations.maps_embed_url', '');
        return $data;
    }
}
