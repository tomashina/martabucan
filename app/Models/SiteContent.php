<?php
namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
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

    public static function defaults(): array { return static::merge(config('content'), config('cms_defaults', [])); }

    public static function section(string $key): array
    {
        $defaults = static::defaults()[$key] ?? [];
        $stored = static::where('key', $key)->first();
        return $stored ? static::merge($defaults, $stored->value) : $defaults;
    }

    public static function allSections(): array
    {
        $stored = static::all()->pluck('value', 'key');
        $data = collect(static::defaults())->map(fn ($value, $key) => $stored->has($key) ? static::merge($value, $stored[$key]) : $value)->all();
        $data['intro']['about_html'] ??= RichText::html($data['intro']['about_text']).RichText::html($data['intro']['about_detail']);
        $data['intro']['results_html'] ??= RichText::html($data['intro']['results_intro']).RichText::html($data['intro']['results_text']);
        $data['settings']['ga4_id'] ??= config('integrations.ga4_measurement_id', '');
        $data['settings']['maps_url'] ??= config('integrations.maps_embed_url', '');
        return $data;
    }
}
