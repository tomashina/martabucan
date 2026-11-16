<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $legacy = array_map(fn ($number) => '/assets/images/zubi'.$number.'-1058.webp', ['01', '02', '03', '04']);

        foreach (['media' => 'hr', 'de.media' => 'de'] as $key => $locale) {
            $record = DB::table('site_contents')->where('key', $key)->first();
            if (!$record) continue;
            $media = json_decode($record->value, true, 512, JSON_THROW_ON_ERROR);
            $paths = array_column($media['results'] ?? [], 'image');
            // Replace the former gallery while preserving independently edited CMS photos.
            if (!$paths || array_diff($paths, $legacy)) continue;

            $media['results'] = array_map(fn ($number) => [
                'image' => '/assets/images/rezultati-'.$number.'-1920.webp',
                'alt' => $locale === 'de' ? 'Fotografischer Vergleich vor und nach der Zahnbehandlung' : 'Fotografski prikaz stanja prije i poslije dentalne terapije',
                'caption' => $locale === 'de' ? 'Vor und nach der Behandlung' : 'Prije i poslije terapije',
            ], ['2393', '2491', '2694']);
            DB::table('site_contents')->where('id', $record->id)->update([
                'value' => json_encode($media, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            ]);
        }
    }

    public function down(): void
    {
        // Content replacement is retained on rollback to avoid restoring superseded photos.
    }
};
