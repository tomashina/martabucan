<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        foreach (['media' => 'hr', 'de.media' => 'de'] as $key => $locale) {
            $record = DB::table('site_contents')->where('key', $key)->first();
            if (!$record) continue;

            $media = json_decode($record->value, true, 512, JSON_THROW_ON_ERROR);
            // Sections without a saved gallery already receive all six defaults.
            if (!isset($media['results']) || !is_array($media['results'])) continue;

            $paths = array_column($media['results'], 'image');
            $changed = false;
            foreach (['3003', '6053', '7457'] as $number) {
                $image = '/assets/images/rezultati-'.$number.'-1920.webp';
                if (in_array($image, $paths, true)) continue;

                $media['results'][] = [
                    'image' => $image,
                    'alt' => $locale === 'de' ? 'Fotografischer Vergleich vor und nach der Zahnbehandlung' : 'Fotografski prikaz stanja prije i poslije dentalne terapije',
                    'caption' => $locale === 'de' ? 'Vor und nach der Behandlung' : 'Prije i poslije terapije',
                ];
                $changed = true;
            }

            if ($changed) DB::table('site_contents')->where('id', $record->id)->update([
                'value' => json_encode($media, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Retain published content and any subsequent CMS edits on rollback.
    }
};
