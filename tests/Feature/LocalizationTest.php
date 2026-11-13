<?php
namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use App\Support\SiteLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $this->actingAs(User::create(['name' => 'Editor', 'email' => 'languages@example.test', 'password' => 'test-password', 'is_admin' => true]));
    }

    private function payload(string $page, string $block, string $locale = 'de'): array
    {
        $site = SiteContent::allSections($locale);
        $values = [];
        foreach (config("cms.pages.$page.blocks.$block.fields") as $key => $field) $values[$key] = data_get($site, $field['bind']);
        return ['revision' => hash('sha256', json_encode($values)), 'fields' => $values];
    }

    public function test_german_pages_have_localized_content_navigation_metadata_and_consent(): void
    {
        $this->get('/de')->assertOk()
            ->assertSee('<html lang="de">', false)
            ->assertSee('Weil Sie und Ihre Familie')->assertSee('nur das Beste verdienen!')
            ->assertSee('Wurzelkanalbehandlung')->assertSee('Herausnehmbarer und festsitzender Zahnersatz')
            ->assertSee('Nataša Stojanović Hindić')->assertSee('Koblenz')
            ->assertSee('href="/de#tim"', false)->assertSee('href="/de/preise"', false)
            ->assertSee('href="/de/datenschutz"', false)
            ->assertSee('content="de_DE"', false)->assertSee('hreflang="hr"', false)
            ->assertSee('Cookie-Einstellungen')->assertSee('Optionale ablehnen')->assertSee('data-pause-label="Bildwechsel pausieren"', false)
            ->assertDontSee('Naručite se')->assertDontSee('Sva prava pridržana')->assertDontSee('<iframe', false);
        foreach (SiteLocale::PATHS['de'] as $page => $path) {
            $this->get('https://clinic.example'.$path)->assertOk()->assertSee('<html lang="de">', false)
                ->assertSee('href="'.SiteLocale::PATHS['hr'][$page].'" lang="hr"', false)
                ->assertSee('hreflang="de" href="https://clinic.example'.$path.'"', false);
        }
        $this->get('/de/preise')->assertSee('Auf Anfrage')->assertSee('Barzahlung')->assertDontSee('Na upit');
        $this->get('/de/datenschutz')->assertSee('Datenschutzerklärung')->assertSee('Die Analyse ist derzeit nicht aktiv.');
        $this->get('/de/cookies')->assertSee('mb_consent_v2')->assertSee('XSRF-TOKEN')->assertSee('180 Tage');
        $this->get('/')->assertSee('<html lang="hr">', false)->assertSee('Podarite sebi i svojoj obitelji');
        $this->get('/de.html')->assertStatus(301)->assertRedirect('/de');
        $this->get('/de/does-not-exist')->assertNotFound();
        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_german_editors_roundtrip_content_without_touching_croatian_sections(): void
    {
        $this->login();
        $croatian = SiteContent::allSections();
        $this->get('/admin?lang=de')->assertOk()->assertSee('Njemački · Deutsch')->assertSee('href="/admin/pages/home?lang=de"', false);
        foreach (config('cms.pages') as $page => $definition) {
            foreach ($definition['blocks'] as $block => $blockDefinition) {
                $path = "/admin/pages/$page/$block?lang=de";
                $this->get($path)->assertOk()->assertSee('Njemački · Deutsch')->assertSee('action="'.$path.'"', false);
                $this->put($path, $this->payload($page, $block))->assertRedirect($path)->assertSessionHasNoErrors();
            }
        }
        $this->assertSame($croatian, SiteContent::allSections());
        $this->assertCount(9, SiteContent::section('services', 'de'));
        $this->assertCount(4, SiteContent::section('team', 'de'));
        $this->get('/de')->assertOk()->assertSee('Weil Sie und Ihre Familie');
    }

    public function test_language_edits_are_isolated_and_stale_german_forms_are_rejected(): void
    {
        $this->login();
        $german = $this->payload('home', 'hero');
        $croatian = $this->payload('home', 'hero', 'hr');
        $croatian['fields']['title'] = 'Novi hrvatski naslov';
        $this->put('/admin/pages/home/hero', $croatian)->assertSessionHasNoErrors();
        $german['fields']['title'] = 'Ihr persönlicher Zahnarzt';
        $this->put('/admin/pages/home/hero?lang=de', $german)->assertSessionHasNoErrors()->assertRedirect('/admin/pages/home/hero?lang=de');
        $this->get('/de')->assertSee('Ihr persönlicher Zahnarzt')->assertDontSee('Novi hrvatski naslov');
        $this->get('/')->assertSee('Novi hrvatski naslov')->assertDontSee('Ihr persönlicher Zahnarzt');
        $this->put('/admin/pages/home/hero?lang=de', $german)->assertSessionHasErrors('revision');
        $this->assertDatabaseHas('site_contents', ['key' => 'de.intro']);
        $this->assertDatabaseHas('site_contents', ['key' => 'intro']);
    }

    public function test_empty_german_repeaters_do_not_restore_defaults_or_remove_croatian_rows(): void
    {
        $this->login();
        $payload = $this->payload('home', 'services');
        $payload['fields']['items'] = '';
        $this->put('/admin/pages/home/services?lang=de', $payload)->assertSessionHasNoErrors();
        $this->assertSame([], SiteContent::section('services', 'de'));
        $this->get('/de')->assertDontSee('class="service-row"', false);
        $this->assertCount(9, SiteContent::section('services'));
        $this->get('/')->assertSee('Preventiva');
    }

    public function test_seo_is_localized_while_domains_and_integrations_are_shared(): void
    {
        $this->login();
        $originalTitle = SiteContent::allSections()['settings']['meta_title'];
        $payload = $this->payload('settings', 'seo');
        $payload['fields']['meta_title'] = 'Deutscher Seitentitel';
        $payload['fields']['site_url'] = 'https://clinic.example';
        $this->put('/admin/pages/settings/seo?lang=de', $payload)->assertSessionHasNoErrors();
        $payload = $this->payload('settings', 'google');
        $payload['fields']['ga4_id'] = 'G-LANG123';
        $this->put('/admin/pages/settings/google?lang=de', $payload)->assertSessionHasNoErrors();
        $this->get('/de/preise')->assertSee('Deutscher Seitentitel')->assertSee('data-ga4-id="G-LANG123"', false)
            ->assertSee('href="https://clinic.example/de/preise"', false)->assertSee('href="https://clinic.example/cjenik"', false);
        $this->get('/')->assertSee($originalTitle)->assertDontSee('Deutscher Seitentitel')->assertSee('data-ga4-id="G-LANG123"', false);
        $payload = $this->payload('settings', 'google', 'hr');
        $payload['fields']['ga4_id'] = '';
        $this->put('/admin/pages/settings/google', $payload)->assertSessionHasNoErrors();
        $this->get('/de')->assertDontSee('data-ga4-id=', false);
    }

    public function test_interface_labels_and_safe_html_can_be_edited_in_german(): void
    {
        $this->login();
        $payload = $this->payload('general', 'ui-navigation');
        $payload['fields']['nav_team'] = 'Praxisteam';
        $this->put('/admin/pages/general/ui-navigation?lang=de', $payload)->assertSessionHasNoErrors();
        $payload = $this->payload('home', 'about');
        $payload['fields']['description'] = '<p>Unser <strong>Team</strong>.</p><script>alert(1)</script>';
        $this->put('/admin/pages/home/about?lang=de', $payload)->assertSessionHasNoErrors();
        $this->get('/de')->assertSee('Praxisteam')->assertSee('<strong>Team</strong>', false)->assertDontSee('alert(1)');
        $this->get('/')->assertSee('Naš tim')->assertDontSee('Praxisteam');
    }

    public function test_invalid_languages_and_unauthorized_language_edits_cannot_change_content(): void
    {
        $payload = $this->payload('home', 'hero');
        $this->put('/admin/pages/home/hero?lang=de', $payload)->assertRedirect('/admin/prijava');
        $this->login();
        $this->get('/admin?lang=fr')->assertStatus(400);
        $this->get('/admin/pages/home?lang[]=de')->assertStatus(400);
        $this->put('/admin/pages/home/hero?lang=fr', $payload)->assertStatus(400);
        $this->assertDatabaseCount('site_contents', 0);
    }
}
