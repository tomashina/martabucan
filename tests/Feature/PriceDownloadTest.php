<?php

namespace Tests\Feature;

use App\Models\PriceListVersion;
use App\Models\SiteContent;
use App\Models\User;
use Database\Seeders\PriceListSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function payload(string $locale = 'hr'): array
    {
        $values = [];
        $site = SiteContent::allSections($locale);
        foreach (config('cms.pages.pricing.blocks.content.fields') as $key => $field) $values[$key] = data_get($site, $field['bind']);
        return ['revision' => hash('sha256', json_encode($values)), 'fields' => $values];
    }

    private function login(): void
    {
        $this->actingAs(User::create(['name' => 'Editor', 'email' => 'xml@example.test', 'password' => 'test-password', 'is_admin' => true]));
    }

    public function test_public_xml_has_all_services_dates_and_required_fields_without_backdating(): void
    {
        $this->seed(PriceListSeeder::class);
        foreach (['hr' => '/cjenik.xml', 'de' => '/de/preise.xml'] as $locale => $url) {
            $version = PriceListVersion::where('locale', $locale)->firstOrFail();
            $this->get($url)->assertRedirect(route('pricing.download', $version))->assertHeader('Cache-Control', 'no-store, private');
            $response = $this->get(route('pricing.download', $version))->assertOk()
                ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
                ->assertHeader('Content-Disposition', 'attachment; filename="'.$version->filename().'"')
                ->assertHeader('Access-Control-Allow-Origin', '*');
            $xml = simplexml_load_string($response->getContent());
            $this->assertNotFalse($xml);
            $this->assertCount(36, $xml->usluge->usluga);
            $this->assertSame('20.00', (string) $xml->usluge->usluga[0]->maloprodajna_cijena);
            $this->assertSame('20.00', (string) $xml->usluge->usluga[0]->sidrena_cijena);
            $this->assertSame('2026-11-16', (string) $xml->usluge->usluga[0]->sidrena_cijena['datum']);
            $this->assertSame('false', (string) $xml->usluge->usluga[0]->posebni_oblik_prodaje->primijenjen);
            $this->assertSame('01', (string) $xml->usluzni_objekt->oznaka);
            $this->assertSame('EUR', (string) $xml['valuta']);
            $this->assertStringStartsWith('ordinacija-dentalne-medicine_', $version->filename());
            $this->assertStringContainsString('_01_'.$version->id.'_', $version->filename());
            $this->assertStringEndsWith('_'.$locale.'.xml', $version->filename());
        }
        $this->get('/cjenik')->assertSee('Preuzmi cjenik (XML)')->assertSee('Arhiva cjenika');
        $this->get('/de/preise')->assertSee('Preisliste herunterladen (XML)')->assertSee('Preislistenarchiv');
        $this->get('/robots.txt')->assertSee('Allow: /cjenik.xml', false)->assertSee('Allow: /cjenik/datoteke/', false);
        $this->get('/cjenik/datoteke/999999.xml')->assertNotFound();
    }

    public function test_price_changes_archive_immutable_versions_and_reverting_prices_creates_a_new_version(): void
    {
        $this->seed(PriceListSeeder::class);
        $original = PriceListVersion::where('locale', 'hr')->firstOrFail();
        $originalXml = $this->get(route('pricing.download', $original))->getContent();
        $originalData = SiteContent::section('prices');
        $changed = $originalData;
        $changed['items'][0]['amount'] = 25;
        $this->travel(31)->days();
        SiteContent::updateOrCreate(['key' => 'prices'], ['value' => $changed]);
        $current = PriceListVersion::where('locale', 'hr')->latest('id')->firstOrFail();
        $this->assertNotSame($original->id, $current->id);
        $this->assertSame($originalXml, $this->get(route('pricing.download', $original))->assertOk()->getContent());
        $this->get('/cjenik/arhiva')->assertSee($original->filename())->assertSee($current->filename());
        $this->get('/cjenik.xml')->assertRedirect(route('pricing.download', $current));
        $changed['note'] = 'Samo novi uvodni tekst';
        SiteContent::updateOrCreate(['key' => 'prices'], ['value' => $changed]);
        $this->assertSame(2, PriceListVersion::where('locale', 'hr')->count());
        SiteContent::updateOrCreate(['key' => 'prices'], ['value' => $originalData]);
        $this->assertSame(3, PriceListVersion::where('locale', 'hr')->count());
        $this->assertSame(1, PriceListVersion::where('locale', 'de')->count());
        $this->travelBack();
    }

    public function test_admin_edits_publish_xml_with_per_service_dates_and_safe_special_sale_text(): void
    {
        $this->seed(PriceListSeeder::class);
        $this->login();
        $payload = $this->payload();
        $payload['fields']['anchor_date'] = '2026-09-10';
        $payload['fields']['items'][0]['amount'] = 30;
        $payload['fields']['items'][0]['special_sale'] = true;
        $payload['fields']['items'][0]['special_sale_name'] = 'Akcija & pregled <kontrola>';
        $payload['fields']['items'][1]['anchor_date'] = '2026-11-16';
        $this->put('/admin/pages/pricing/content', $payload)->assertSessionHasNoErrors();
        $version = PriceListVersion::where('locale', 'hr')->latest('id')->firstOrFail();
        $xml = simplexml_load_string($this->get(route('pricing.download', $version))->assertOk()->getContent());
        $this->assertSame('30.00', (string) $xml->usluge->usluga[0]->maloprodajna_cijena);
        $this->assertSame('20.00', (string) $xml->usluge->usluga[0]->sidrena_cijena);
        $this->assertSame('2026-09-10', (string) $xml->usluge->usluga[0]->sidrena_cijena['datum']);
        $this->assertSame('2026-11-16', (string) $xml->usluge->usluga[1]->sidrena_cijena['datum']);
        $this->assertSame('Akcija & pregled <kontrola>', (string) $xml->usluge->usluga[0]->posebni_oblik_prodaje->naziv);
        $this->get('/cjenik')->assertSee('10. 09. 2026.')->assertSee('16. 11. 2026.')->assertDontSee('<del', false);
        $this->get('/de/preise')->assertDontSee('10. 09. 2026.');
    }

    public function test_incomplete_published_prices_are_rejected_without_changing_the_current_version(): void
    {
        $this->seed(PriceListSeeder::class);
        $this->login();
        $payload = $this->payload();
        $payload['fields']['anchor_date'] = '';
        $this->put('/admin/pages/pricing/content', $payload)->assertSessionHasErrors('fields.anchor_date');
        $payload = $this->payload();
        $payload['fields']['items'][0]['amount'] = null;
        $this->put('/admin/pages/pricing/content', $payload)->assertSessionHasErrors('fields.items.0.amount');
        $payload = $this->payload();
        $payload['fields']['items'][0]['special_sale'] = true;
        $this->put('/admin/pages/pricing/content', $payload)->assertSessionHasErrors('fields.items.0.special_sale_name');
        $this->assertSame(1, PriceListVersion::where('locale', 'hr')->count());
        $this->assertEquals(20, SiteContent::section('prices')['items'][0]['amount']);
        $this->assertSame('2026-11-16', SiteContent::section('prices')['anchor_date']);
    }

    public function test_address_changes_publish_new_versions_without_rewriting_previous_downloads(): void
    {
        $this->seed(PriceListSeeder::class);
        $version = PriceListVersion::where('locale', 'hr')->firstOrFail();
        $original = $version->xml();
        $contact = SiteContent::section('contact');
        $contact['address'] = 'Nova adresa 1';
        SiteContent::updateOrCreate(['key' => 'contact'], ['value' => $contact]);
        $this->assertSame(4, PriceListVersion::count());
        $this->assertSame($original, $version->fresh()->xml());
        $this->assertStringContainsString('nova-adresa-1', PriceListVersion::latest('id')->first()->filename());
    }
}
