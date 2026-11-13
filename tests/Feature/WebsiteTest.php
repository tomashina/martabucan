<?php
namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_legal_pages_render_without_third_party_embeds(): void
    {
        $this->get('/')->assertOk()->assertSee('Podarite sebi i svojoj obitelji')->assertSee('savršeni osmijeh!')->assertSee('href="/cjenik"', false)->assertSee('fa-tooth')->assertDontSee('<iframe', false)->assertDontSee('google-analytics.com')->assertDontSee('fa-arrow-')->assertSee('zubi04-1058.webp')->assertSee('endodoncija');
        $this->get('/cjenik')->assertOk()->assertSee('Na upit');
        foreach (['privatnost', 'kolacici', 'uvjeti-koristenja', 'podaci-o-ordinaciji'] as $page) $this->get('/'.$page)->assertOk();
        $this->get('/index.html')->assertStatus(301)->assertRedirect('/');
    }

    public function test_cms_requires_admin_and_cannot_be_written_by_guests(): void
    {
        $this->get('/admin')->assertRedirect('/admin/prijava');
        $this->put('/admin/intro', ['content' => ['title' => 'Unauthorized']])->assertRedirect('/admin/prijava');
        $user = User::create(['name' => 'Reader', 'email' => 'reader@example.test', 'password' => 'test-only-password', 'is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->put('/admin/intro', ['content' => ['title' => 'Unauthorized']])->assertForbidden();
        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_optional_analytics_is_configured_without_loading_it_before_consent(): void
    {
        config(['integrations.ga4_measurement_id' => '']);
        $this->get('/')->assertOk()->assertDontSee('data-ga4-id=', false)->assertDontSee('id="analytics-consent"', false)->assertDontSee('@if(');
        config(['integrations.ga4_measurement_id' => 'G-TEST123']);
        $this->get('/')->assertOk()->assertSee('data-ga4-id="G-TEST123"', false)->assertSee('id="analytics-consent"', false)->assertDontSee('<iframe', false)->assertDontSee('src="https://www.googletagmanager.com', false)->assertDontSee('analytics.js');
        $this->get('/kolacici')->assertOk()->assertSee('mb_consent_v2')->assertSee('XSRF-TOKEN')->assertSee('180 dana')->assertDontSee('Analitika trenutačno nije aktivna');
        config(['integrations.ga4_measurement_id' => 'UA-172128-36']);
        $this->get('/')->assertDontSee('data-ga4-id=', false)->assertDontSee('id="analytics-consent"', false);
    }

    public function test_admin_can_save_content_and_text_is_escaped(): void
    {
        $admin = $this->admin();
        $content = config('content.intro');
        $content['title'] = '<script>alert("unsafe")</script>';
        $this->actingAs($admin)->put('/admin/intro', ['content' => $content])->assertRedirect();
        $this->get('/')->assertOk()->assertSee($content['title'])->assertDontSee($content['title'], false);
    }

    public function test_price_amounts_from_labels_and_empty_prices_render_correctly(): void
    {
        $this->actingAs($this->admin());
        $prices = config('content.prices');
        $prices['items'][0]['amount'] = '45.50';
        $prices['items'][0]['from'] = true;
        $prices['items'][1]['amount'] = '0';
        $this->put('/admin/prices', ['content' => $prices])->assertRedirect()->assertSessionHasNoErrors();
        $this->get('/cjenik')->assertSee('45,50 €')->assertSee('0,00 €')->assertSee('Na upit')->assertSee('<small>od </small>', false);
        $prices['items'][0]['amount'] = null;
        $this->put('/admin/prices', ['content' => $prices])->assertSessionHasNoErrors();
        $this->get('/cjenik')->assertDontSee('45,50 €');
    }

    public function test_invalid_prices_contact_details_and_dates_are_rejected(): void
    {
        $this->actingAs($this->admin());
        $prices = config('content.prices');
        $prices['items'][0]['amount'] = -12;
        $prices['valid_from'] = 'not-a-date';
        $this->put('/admin/prices', ['content' => $prices])->assertSessionHasErrors(['content.items.0.amount', 'content.valid_from']);
        $contact = config('content.contact'); $contact['email'] = 'not-email';
        $this->put('/admin/contact', ['content' => $contact])->assertSessionHasErrors('content.email');
        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_all_admin_sections_render_and_asset_fields_cannot_be_overwritten(): void
    {
        $this->actingAs($this->admin());
        foreach (array_keys(config('content')) as $section) $this->get('/admin/'.$section)->assertOk();
        $team = config('content.team'); $team[0]['image'] = 'https://example.test/tracker';
        $this->put('/admin/team', ['content' => $team])->assertSessionHasNoErrors();
        $this->assertSame('marta.webp', SiteContent::section('team')[0]['image']);
        $this->get('/admin/unknown')->assertNotFound();
    }

    public function test_login_accepts_only_admins_and_logout_revokes_session(): void
    {
        $admin = $this->admin();
        $this->post('/admin/prijava', ['email' => $admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/prijava', ['email' => $admin->email, 'password' => 'test-only-password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->post('/admin/odjava')->assertRedirect('/admin/prijava');
        $this->assertGuest();
    }

    private function admin(): User
    {
        return User::create(['name' => 'Test admin', 'email' => 'admin@example.test', 'password' => 'test-only-password', 'is_admin' => true]);
    }
}
