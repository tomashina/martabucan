<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Database\Seeders\PriceListSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_import_sets_all_prices_and_categories_in_both_languages(): void
    {
        SiteContent::create(['key' => 'prices', 'value' => ['title' => 'Vlastiti naslov', 'tax_note' => 'Sačuvana napomena']]);
        SiteContent::create(['key' => 'intro', 'value' => ['title' => 'Naslovnica']]);
        $this->seed(PriceListSeeder::class);

        foreach (['hr' => '/cjenik', 'de' => '/de/preise'] as $locale => $url) {
            $prices = SiteContent::section('prices', $locale);
            $this->assertCount(36, $prices['items']);
            $this->assertSame([5, 8, 4, 3, 10, 3, 3], array_values(array_count_values(array_column($prices['items'], 'category'))));
            $this->assertSame('2026-11-16', $prices['valid_from']);
            // PDF visual reading: periodontal prices are 40/33/40, extractions 20/40/50.
            $this->assertSame([40, 33, 40], array_column(array_slice($prices['items'], 17, 3), 'amount'));
            $this->assertSame([20, 40, 50], array_column(array_slice($prices['items'], 30, 3), 'amount'));
            foreach ($prices['items'] as $item) {
                $this->assertSame($item['amount'], $item['anchor_amount']);
                $this->assertFalse($item['from']);
            }
            $response = $this->get($url)->assertOk()->assertSee('16. 11. 2026.')
                ->assertDontSee('<del', false)->assertSee('class="price-reference"', false);
            $this->assertSame(36, substr_count($response->getContent(), 'class="price-reference"'));
            $this->assertSame(7, substr_count($response->getContent(), 'class="price-category-title"'));
            $this->assertSame(36, substr_count($response->getContent(), 'class="price-row"'));
            $response->assertSeeInOrder(array_column($prices['items'], 'name'));
        }
        $this->assertSame('Vlastiti naslov', SiteContent::section('prices')['title']);
        $this->assertSame('Sačuvana napomena', SiteContent::section('prices')['tax_note']);
        $this->assertSame('Naslovnica', SiteContent::section('intro')['title']);
        $before = SiteContent::section('prices');
        $this->seed(PriceListSeeder::class);
        $this->assertSame($before, SiteContent::section('prices'));
        $this->assertDatabaseCount('site_contents', 3);
    }

    public function test_categories_roundtrip_in_admin_and_group_safely_without_duplicate_headers(): void
    {
        $this->actingAs(User::create(['name' => 'Editor', 'email' => 'pricing@example.test', 'password' => 'test-password', 'is_admin' => true]));
        $site = SiteContent::allSections();
        $fields = [];
        foreach (config('cms.pages.pricing.blocks.content.fields') as $key => $field) $fields[$key] = data_get($site, $field['bind']);
        $revision = hash('sha256', json_encode($fields));
        $fields['anchor_date'] = '2026-09-10';
        $row = fn ($name, $category) => ['name' => $name, 'category' => $category, 'detail' => '', 'amount' => 20, 'anchor_amount' => 20, 'from' => false];
        $fields['items'] = [$row('Prva', 'Protetika'), $row('Druga', 'Kirurgija'), $row('Treća', 'Protetika'), $row('Četvrta', '<script>unsafe</script>'), $row('Bez kategorije', '')];
        $this->put('/admin/pages/pricing/content', compact('revision', 'fields'))->assertSessionHasNoErrors();
        $this->assertSame('Protetika', SiteContent::section('prices')['items'][0]['category']);
        $this->get('/admin/pages/pricing')->assertOk()->assertSee('Kategorija');
        $response = $this->get('/cjenik')->assertOk()->assertSeeInOrder(['Protetika', 'Prva', 'Treća', 'Kirurgija', 'Druga'])
            ->assertSee('Bez kategorije')->assertSee('<script>unsafe</script>')->assertDontSee('<script>unsafe</script>', false);
        $this->assertSame(3, substr_count($response->getContent(), 'class="price-category-title"'));
    }
}
