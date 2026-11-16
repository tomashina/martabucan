<?php
namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use App\Support\ImageAsset;
use App\Support\RichText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $this->actingAs(User::create(['name'=>'CMS editor','email'=>'cms@example.test','password'=>'test-password','is_admin'=>true]));
    }

    private function payload(string $page, string $block): array
    {
        $site=SiteContent::allSections(); $values=[];
        foreach (config("cms.pages.$page.blocks.$block.fields") as $key=>$field) $values[$key]=data_get($site,$field['bind']);
        return ['revision'=>hash('sha256',json_encode($values)),'fields'=>$values];
    }

    public function test_every_editor_renders_and_roundtrips_existing_content_without_losing_lists(): void
    {
        $this->login(); $original=SiteContent::allSections();
        $this->get('/admin')->assertOk()->assertSee('Pregled stranica')->assertSee('brand-wordmark',false);
        foreach (config('cms.pages') as $page=>$definition) {
            foreach ($definition['blocks'] as $block=>$blockDefinition) {
                $this->get("/admin/pages/$page/$block")->assertOk()->assertSee('Spremi promjene');
                $this->put("/admin/pages/$page/$block",$this->payload($page,$block))->assertRedirect("/admin/pages/$page/$block")->assertSessionHasNoErrors();
            }
            $this->get($definition['url'])->assertOk();
        }
        $current=SiteContent::allSections();
        foreach (['services','team','faq'] as $key) $this->assertCount(count($original[$key]),$current[$key]);
        $this->assertSame($original['media'],$current['media']);
        $this->assertSame($original['intro']['title'],$current['intro']['title']);
        $this->assertSame($original['contact'],$current['contact']);
        $this->get('/')->assertSee('Dobar osjećaj počinje')->assertSee('rezultati-2694-1920.webp');
    }

    public function test_repeater_create_reorder_delete_and_empty_list_are_reflected_on_front(): void
    {
        $this->login(); $payload=$this->payload('home','services');
        $payload['fields']['items']=[7=>['title'=>'Nova usluga','icon'=>'tooth','description'=>'<p>Prvi <strong>opis</strong>.</p>'],2=>$payload['fields']['items'][0]];
        $this->put('/admin/pages/home/services',$payload)->assertSessionHasNoErrors();
        $this->assertSame('Nova usluga',SiteContent::section('services')[0]['title']);
        $this->assertCount(2,SiteContent::section('services'));
        $this->get('/')->assertSeeInOrder(['Nova usluga','Preventiva'])->assertSee('<strong>opis</strong>',false)->assertDontSee('Estetska stomatologija');
        $payload=$this->payload('home','services'); $payload['fields']['items']='';
        $this->put('/admin/pages/home/services',$payload)->assertSessionHasNoErrors();
        $this->assertSame([],SiteContent::allSections()['services']);
        $this->get('/')->assertOk()->assertDontSee('class="service-row"',false);
        $payload=$this->payload('home','faq'); $payload['fields']['items']=[['question'=>'Novo pitanje?','answer'=>'<p>Odgovor s <em>naglašenim tekstom</em>.</p>']];
        $this->put('/admin/pages/home/faq',$payload)->assertSessionHasNoErrors();
        $this->get('/')->assertSee('Novo pitanje?')->assertSee('<em>naglašenim tekstom</em>',false)->assertDontSee('Kako dogovoriti prvi pregled?');
    }

    public function test_safe_html_is_rendered_without_scripts_styles_or_unsafe_urls(): void
    {
        $this->login(); $payload=$this->payload('home','about');
        $payload['fields']['description']='<div onclick="alert(1)">Siguran <strong>tekst</strong><script>alert(2)</script></div><p style="position:fixed">Opis <a href="javascript:alert(3)">link</a><img src="https://tracker.test/a"></p>';
        $this->put('/admin/pages/home/about',$payload)->assertSessionHasNoErrors();
        $this->get('/')->assertSee('<strong>tekst</strong>',false)->assertSee('<p class="cms-copy lead">',false)->assertDontSee('alert(')->assertDontSee('tracker.test')->assertDontSee('position:fixed');
        $this->assertStringContainsString('<h2 class="cms-copy">',RichText::render('<h1>Manji naslov</h1>'));
        $this->assertStringNotContainsString('<div',RichText::render('<div>Prvi</div><div>Drugi</div>'));
    }

    public function test_saving_a_block_preserves_other_blocks_and_stale_form_is_rejected(): void
    {
        $this->login(); $original=SiteContent::allSections(); $hero=$this->payload('home','hero'); $about=$this->payload('home','about');
        $hero['fields']['title']='Uređeni naslov';
        $this->put('/admin/pages/home/hero',$hero)->assertSessionHasNoErrors();
        $about['fields']['title']='Uređeno o nama';
        $this->put('/admin/pages/home/about',$about)->assertSessionHasNoErrors();
        $site=SiteContent::allSections();
        $this->assertSame('Uređeni naslov',$site['intro']['title']);
        $this->assertSame('Uređeno o nama',$site['intro']['about_title']);
        $this->assertSame($original['team'],$site['team']);
        $this->put('/admin/pages/home/hero',$hero)->assertSessionHasErrors('revision');
    }

    public function test_image_upload_generates_local_webp_variants_and_front_uses_them(): void
    {
        $this->login(); $payload=$this->payload('home','about'); $before=SiteContent::allSections()['media']['about'];
        $payload['uploads']['image']=UploadedFile::fake()->image('new-team.jpg',1400,900);
        $paths=[];
        try {
            $this->put('/admin/pages/home/about',$payload)->assertSessionHasNoErrors();
            $image=SiteContent::allSections()['media']['about'];
            $this->assertStringStartsWith('/assets/uploads/',$image);
            foreach ([480,960,1920] as $width) {
                $path=public_path(str_replace('-1920.webp',"-$width.webp",$image)); $paths[]=$path;
                $this->assertFileExists($path); $info=getimagesize($path);
                $this->assertSame('image/webp',$info['mime']); $this->assertLessThanOrEqual($width,$info[0]);
            }
            $this->get('/')->assertSee($image,false)->assertSee(str_replace('-1920.webp','-480.webp',$image),false);
            $this->assertFileExists(public_path($before));
        } finally { foreach($paths as $path) if(is_file($path)) unlink($path); }
    }

    public function test_invalid_uploads_paths_fields_and_empty_required_images_are_atomic(): void
    {
        $this->login(); $payload=$this->payload('home','about');
        $payload['fields']['title']='Must not be saved'; $payload['fields']['image']='../../.env';
        $this->put('/admin/pages/home/about',$payload)->assertSessionHasErrors('fields.image');
        $payload=$this->payload('home','about'); $payload['uploads']['image']=UploadedFile::fake()->createWithContent('x.svg','<svg xmlns="http://www.w3.org/2000/svg"></svg>');
        $this->put('/admin/pages/home/about',$payload)->assertSessionHasErrors('uploads.image');
        $payload=$this->payload('home','hero'); $payload['fields']['slides']=[];
        $this->put('/admin/pages/home/hero',$payload)->assertSessionHasErrors('fields.slides');
        $payload=$this->payload('home','about'); $payload['fields']['image']='';
        $this->put('/admin/pages/home/about',$payload)->assertSessionHasErrors('fields.image');
        $payload=$this->payload('home','about'); $payload['fields']['injected']='extra';
        $this->put('/admin/pages/home/about',$payload)->assertSessionHasErrors('fields');
        $this->assertDatabaseCount('site_contents',0);
        $this->assertFalse(ImageAsset::allowed('https://example.test/tracker.webp'));
    }

    public function test_new_routes_enforce_admin_access(): void
    {
        $payload=$this->payload('home','about');
        $this->get('/admin/pages/home/about')->assertRedirect('/admin/prijava');
        $this->put('/admin/pages/home/about',$payload)->assertRedirect('/admin/prijava');
        $this->actingAs(User::create(['name'=>'Reader','email'=>'reader@cms.test','password'=>'test','is_admin'=>false]));
        $this->get('/admin/pages/home/about')->assertForbidden();
        $this->put('/admin/pages/home/about',$payload)->assertForbidden();
        $this->assertDatabaseCount('site_contents',0);
    }
}
