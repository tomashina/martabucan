<?php
namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::create(['name'=>'Administrator','email'=>'admin@clinic.test','password'=>'existing-test-password','is_admin'=>true]);
        $user->forceFill(['is_master'=>true])->save();
        return $user;
    }

    private function payload(string $page,string $block): array
    {
        $values=[]; $site=SiteContent::allSections();
        foreach(config("cms.pages.$page.blocks.$block.fields") as $key=>$field) $values[$key]=data_get($site,$field['bind']);
        return ['revision'=>hash('sha256',json_encode($values)),'fields'=>$values];
    }

    public function test_cms_settings_update_metadata_profiles_and_consent_guarded_analytics(): void
    {
        $this->actingAs($this->admin());
        $payload=$this->payload('settings','seo');
        $payload['fields']['meta_title']='Novi SEO naslov'; $payload['fields']['meta_description']='Opis ordinacije za tražilice'; $payload['fields']['site_url']='https://clinic.example';
        $this->put('/admin/pages/settings/seo',$payload)->assertSessionHasNoErrors();
        $payload=$this->payload('settings','sharing'); $payload['fields']['og_title']='Naslov za Facebook'; $payload['fields']['og_description']='Opis poveznice';
        $this->put('/admin/pages/settings/sharing',$payload)->assertSessionHasNoErrors();
        $payload=$this->payload('settings','google'); $payload['fields']['ga4_id']='G-CMS123'; $payload['fields']['google_verification']='google_token-123=';
        $this->put('/admin/pages/settings/google',$payload)->assertSessionHasNoErrors();
        $payload=$this->payload('settings','social'); $payload['fields']['facebook_url']='https://www.facebook.com/clinic'; $payload['fields']['instagram_url']='https://www.instagram.com/clinic'; $payload['fields']['facebook_verification']='meta-token';
        $this->put('/admin/pages/settings/social',$payload)->assertSessionHasNoErrors();
        $this->get('/')->assertSee('<title>Novi SEO naslov</title>',false)->assertSee('property="og:title" content="Naslov za Facebook"',false)->assertSee('property="og:url" content="https://clinic.example/"',false)->assertSee('name="google-site-verification" content="google_token-123="',false)->assertSee('name="facebook-domain-verification" content="meta-token"',false)->assertSee('href="https://www.facebook.com/clinic"',false)->assertSee('data-ga4-id="G-CMS123"',false)->assertSee('id="analytics-consent"',false)->assertDontSee('src="https://www.googletagmanager.com',false)->assertDontSee('<iframe',false);
        $this->get('/cjenik')->assertSee('property="og:url" content="https://clinic.example/cjenik"',false);
        $this->get('/privatnost')->assertDontSee('Analitika trenutačno nije aktivna');
        $payload=$this->payload('settings','google'); $payload['fields']['ga4_id']='';
        $this->put('/admin/pages/settings/google',$payload)->assertSessionHasNoErrors();
        $this->get('/')->assertDontSee('data-ga4-id=',false)->assertDontSee('id="analytics-consent"',false);
        $this->get('/privatnost')->assertSee('Analitika trenutačno nije aktivna');
    }

    public function test_invalid_integration_codes_and_urls_are_rejected(): void
    {
        $this->actingAs($this->admin());
        $payload=$this->payload('settings','google'); $payload['fields']['ga4_id']='UA-172128-36'; $payload['fields']['google_verification']='<script>alert(1)</script>'; $payload['fields']['maps_url']='https://untrusted.example/embed';
        $this->put('/admin/pages/settings/google',$payload)->assertSessionHasErrors(['fields.ga4_id','fields.google_verification','fields.maps_url']);
        $payload=$this->payload('settings','social'); $payload['fields']['facebook_url']='javascript:alert(1)';
        $this->put('/admin/pages/settings/social',$payload)->assertSessionHasErrors('fields.facebook_url');
        $payload=$this->payload('settings','seo'); $payload['fields']['site_url']='https://clinic.example/path?x=1';
        $this->put('/admin/pages/settings/seo',$payload)->assertSessionHasErrors('fields.site_url');
        $this->assertDatabaseCount('site_contents',0);
    }

    public function test_local_site_remains_noindex_and_production_robots_follow_the_setting(): void
    {
        SiteContent::create(['key'=>'settings','value'=>['indexable'=>true]]);
        $this->get('http://martabucan.test/')->assertSee('content="noindex, nofollow"',false);
        $this->get('http://martabucan.test/robots.txt')->assertSee("Disallow: /\n",false);
        $this->get('https://clinic.example/')->assertSee('content="index, follow"',false);
        $this->get('https://clinic.example/robots.txt')->assertSee('Disallow: /admin',false);
    }

    public function test_anchor_prices_accept_equal_values_and_only_show_higher_values_as_discounts(): void
    {
        $this->actingAs($this->admin()); $payload=$this->payload('pricing','content');
        $payload['fields']['items'][0]['amount']='80'; $payload['fields']['items'][0]['anchor_amount']='120';
        $this->put('/admin/pages/pricing/content',$payload)->assertSessionHasNoErrors();
        $this->assertEquals(120,SiteContent::section('prices')['items'][0]['anchor_amount']);
        $this->get('/cjenik')->assertSee('<del class="price-anchor">',false)->assertSee('120,00 €')->assertSee('80,00 €');
        $payload=$this->payload('pricing','content'); $payload['fields']['items'][0]['anchor_amount']='80';
        $payload['fields']['valid_from']='2026-11-16';
        $this->put('/admin/pages/pricing/content',$payload)->assertSessionHasNoErrors();
        $this->assertEquals(80,SiteContent::section('prices')['items'][0]['anchor_amount']);
        $this->get('/cjenik')->assertDontSee('<del class="price-anchor">',false)->assertSee('80,00 €')->assertSee('16. 11. 2026.');
        $payload=$this->payload('pricing','content'); $payload['fields']['items'][0]['anchor_amount']='70';
        $this->put('/admin/pages/pricing/content',$payload)->assertSessionHasErrors('fields.items.0.anchor_amount');
        $payload=$this->payload('pricing','content'); $payload['fields']['items'][0]['amount']='';
        $this->put('/admin/pages/pricing/content',$payload)->assertSessionHasErrors('fields.items.0.amount');
        $payload=$this->payload('pricing','content'); $payload['fields']['items'][0]['anchor_amount']='';
        $this->put('/admin/pages/pricing/content',$payload)->assertSessionHasNoErrors();
        $this->get('/cjenik')->assertDontSee('price-anchor',false)->assertSee('80,00 €');
    }

    public function test_icon_selection_is_saved_and_used_by_front_with_a_validated_allowlist(): void
    {
        $this->actingAs($this->admin()); $payload=$this->payload('home','services');
        $this->get('/admin/pages/home/services')->assertSee('role="radiogroup"',false)->assertSee('Pretraži ikone');
        $payload['fields']['items'][0]['icon']='heart';
        $this->put('/admin/pages/home/services',$payload)->assertSessionHasNoErrors();
        $this->assertSame('heart',SiteContent::section('services')[0]['icon']);
        $this->get('/')->assertSee('fa-heart');
        $payload=$this->payload('home','services'); $payload['fields']['items'][0]['icon']='invalid-icon';
        $this->put('/admin/pages/home/services',$payload)->assertSessionHasErrors('fields.items.0.icon');
    }

    public function test_only_admins_can_create_admin_accounts_and_passwords_are_hashed(): void
    {
        $new=['name'=>'Novi administrator','email'=>'New.Admin@clinic.test','password'=>'new-test-password','password_confirmation'=>'new-test-password'];
        $this->get('/admin/users')->assertRedirect('/admin/prijava');
        $this->post('/admin/users',$new)->assertRedirect('/admin/prijava');
        $reader=User::create(['name'=>'Reader','email'=>'reader@clinic.test','password'=>'reader-password','is_admin'=>false]);
        $this->actingAs($reader)->get('/admin/users/create')->assertForbidden();
        $this->actingAs($reader)->post('/admin/users',$new)->assertForbidden();
        $this->withSession(['password_hash_web'=>null])->actingAs($this->admin());
        $this->get('/admin/users/create')->assertOk()->assertSee('Ponovite lozinku');
        $this->post('/admin/users',$new)->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $created=User::where('email','new.admin@clinic.test')->firstOrFail();
        $this->assertTrue($created->is_admin); $this->assertTrue(Hash::check($new['password'],$created->password)); $this->assertNotSame($new['password'],$created->password);
        $this->get('/admin/users')->assertSee('Novi administrator')->assertDontSee($new['password']);
        $this->post('/admin/users',$new)->assertSessionHasErrors('email');
        $this->post('/admin/users',array_merge($new,['email'=>'another@clinic.test','password_confirmation'=>'different']))->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users',['email'=>'another@clinic.test']);
        $this->post('/admin/odjava');
        $this->post('/admin/prijava',['email'=>$created->email,'password'=>$new['password']])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($created);
    }
}
