<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $categories = require database_path('data/prices-2026-11-16.php');
        $notes = [
            'hr' => 'Cijene dentalnih usluga izražene su u eurima i raspoređene prema vrsti zahvata. Individualni plan terapije i trošak dogovaramo prije početka zahvata.',
            'de' => 'Die Preise unserer zahnmedizinischen Leistungen sind in Euro angegeben und nach Behandlungsart geordnet. Den individuellen Therapieplan und die Kosten vereinbaren wir vor Behandlungsbeginn.',
        ];

        DB::transaction(function () use ($categories, $notes) {
            foreach ($notes as $locale => $note) {
                $prices = SiteContent::section('prices', $locale);
                $prices['items'] = [];
                foreach ($categories as $category) {
                    foreach ($category['items'] as $item) {
                        $prices['items'][] = [
                            'name' => $item[$locale],
                            'category' => $category[$locale],
                            'detail' => $item['detail_'.$locale] ?? '',
                            'amount' => $item['amount'],
                            'anchor_amount' => $item['amount'],
                            'from' => false,
                        ];
                    }
                }
                $prices['note'] = $note;
                $prices['valid_from'] = '2026-11-16';
                SiteContent::updateOrCreate(['key' => SiteContent::storageKey('prices', $locale)], ['value' => $prices]);
            }
        });
    }
}
