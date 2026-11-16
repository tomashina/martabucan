<?php

use App\Support\PricePublication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('price_list_versions', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 2)->index();
            $table->string('content_hash', 64);
            $table->json('payload');
            $table->timestamp('published_at');
        });

        foreach (['prices', 'de.prices'] as $key) {
            $record = DB::table('site_contents')->where('key', $key)->first();
            if (!$record) continue;
            $prices = json_decode($record->value, true, 512, JSON_THROW_ON_ERROR);
            // Preserve the date supplied with the imported list; never assert unconfirmed September prices.
            if (empty($prices['anchor_date']) && !empty($prices['valid_from'])) {
                $prices['anchor_date'] = $prices['valid_from'];
                DB::table('site_contents')->where('id', $record->id)->update(['value' => json_encode($prices, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
            }
        }
        foreach (['hr', 'de'] as $locale) app(PricePublication::class)->publish($locale);
    }

    public function down(): void { Schema::dropIfExists('price_list_versions'); }
};
