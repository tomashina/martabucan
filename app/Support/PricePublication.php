<?php

namespace App\Support;

use App\Models\PriceListVersion;
use App\Models\SiteContent;
use Illuminate\Support\Facades\DB;

class PricePublication
{
    public static function anchorDate(array $prices, array $item): string
    {
        return (string) (($item['anchor_date'] ?? '') ?: ($prices['anchor_date'] ?? ''));
    }

    public function payload(string $locale): ?array
    {
        $prices = SiteContent::section('prices', $locale);
        $business = SiteContent::section('business');
        $contact = SiteContent::section('contact');
        $items = [];
        foreach ($prices['items'] as $item) {
            $date = self::anchorDate($prices, $item);
            if (!is_numeric($item['amount'] ?? null) || !is_numeric($item['anchor_amount'] ?? null) || !$date) return null;
            if (($item['special_sale'] ?? false) && !trim($item['special_sale_name'] ?? '')) return null;
            $items[] = [
                'name' => $item['name'], 'category' => $item['category'] ?? '', 'detail' => $item['detail'] ?? '',
                'amount' => number_format((float) $item['amount'], 2, '.', ''),
                'anchor_amount' => number_format((float) $item['anchor_amount'], 2, '.', ''),
                'anchor_date' => $date, 'from' => (bool) ($item['from'] ?? false),
                'special_sale' => (bool) ($item['special_sale'] ?? false),
                'special_sale_name' => ($item['special_sale'] ?? false) ? trim($item['special_sale_name']) : '',
            ];
        }
        if (!$items) return null;
        return [
            'valid_from' => $prices['valid_from'] ?? '',
            'business' => ['name' => $business['name'], 'oib' => $business['oib']],
            'object' => ['type' => $prices['object_type'], 'code' => $prices['object_code'], 'address' => $contact['address'].', '.$contact['city']],
            'items' => $items,
        ];
    }

    private function hash(array $payload): string
    {
        return hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    public function current(string $locale): ?PriceListVersion
    {
        $payload = $this->payload($locale);
        $latest = PriceListVersion::where('locale', $locale)->latest('id')->first();
        return $payload && $latest && hash_equals($latest->content_hash, $this->hash($payload)) ? $latest : null;
    }

    public function publish(string $locale): ?PriceListVersion
    {
        return DB::transaction(function () use ($locale) {
            // Serialize saves for this language. Versions are append-only, including A -> B -> A changes.
            SiteContent::where('key', SiteContent::storageKey('prices', $locale))->lockForUpdate()->first();
            $payload = $this->payload($locale);
            if (!$payload) return null;
            $hash = $this->hash($payload);
            $latest = PriceListVersion::where('locale', $locale)->latest('id')->first();
            if ($latest && hash_equals($latest->content_hash, $hash)) return $latest;
            return PriceListVersion::create(['locale' => $locale, 'content_hash' => $hash, 'payload' => $payload, 'published_at' => now()]);
        });
    }
}
