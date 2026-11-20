<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_both_languages_render_all_six_photos_with_responsive_variants(): void
    {
        foreach (['/' => 'hr', '/de' => 'de'] as $url => $locale) {
            $response = $this->get($url)->assertOk();
            $this->assertCount(6, SiteContent::section('media', $locale)['results']);
            $this->assertSame(6, substr_count($response->getContent(), 'data-gallery="'));
            foreach (['2393', '2491', '2694', '3003', '6053', '7457'] as $number) {
                foreach ([480, 960, 1920] as $width) {
                    $path = '/assets/images/rezultati-'.$number.'-'.$width.'.webp';
                    $response->assertSee($path, false);
                    $this->assertFileExists(public_path($path));
                }
            }
        }
    }

    public function test_migration_preserves_saved_content_and_adds_missing_photos_only_once(): void
    {
        $saved = [];
        foreach (['media' => 'hr', 'de.media' => 'de'] as $key => $locale) {
            $saved[$key] = SiteContent::defaults($locale)['media'];
            $saved[$key]['results'] = array_slice($saved[$key]['results'], 0, 4);
            $saved[$key]['results'][0]['caption'] = 'Uređeni opis '.$locale;
            $saved[$key]['about_alt'] = 'Uređeni opis ordinacije '.$locale;
            SiteContent::create(['key' => $key, 'value' => $saved[$key]]);
        }

        $migration = require database_path('migrations/2026_11_20_000001_add_missing_result_photos.php');
        $migration->up();
        $migration->up();

        foreach (['media' => 'hr', 'de.media' => 'de'] as $key => $locale) {
            $expected = $saved[$key];
            $expected['results'] = array_merge($expected['results'], array_slice(SiteContent::defaults($locale)['media']['results'], 4));
            $this->assertSame($expected, SiteContent::where('key', $key)->firstOrFail()->value);
        }
    }
}
