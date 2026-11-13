<?php
namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $email = 'editor@clinic.test', bool $master = false, bool $admin = true): User
    {
        $user = User::create(['name'=>'Test administrator', 'email'=>$email, 'password'=>'existing-test-password', 'is_admin'=>$admin]);
        $user->forceFill(['is_master'=>$master])->save();
        return $user;
    }

    private function passwords(): array
    {
        return ['current_password'=>'existing-test-password', 'password'=>'new-test-password', 'password_confirmation'=>'new-test-password'];
    }

    public function test_each_admin_can_change_only_their_own_password_and_remain_signed_in(): void
    {
        $user = $this->user(); $other = $this->user('other@clinic.test');
        $originalHash = $user->password; $otherHash = $other->password;
        $this->actingAs($user)->get('/admin/account')->assertOk()->assertSee('Trenutačna lozinka')->assertDontSee($originalHash);
        $oldSession = session()->getId();
        $this->put('/admin/account/password', $this->passwords() + ['user_id'=>$other->id, 'is_master'=>true])
            ->assertRedirect('/admin/account')->assertSessionHasNoErrors()->assertSessionHas('saved');
        $this->assertTrue(Hash::check('new-test-password', $user->fresh()->password));
        $this->assertNotSame($originalHash, $user->fresh()->password);
        $this->assertFalse($user->fresh()->is_master);
        $this->assertSame($otherHash, $other->fresh()->password);
        $this->assertNotSame($oldSession, session()->getId());
        $this->get('/admin/account')->assertOk();
        $this->assertAuthenticatedAs($user);
        $this->post('/admin/odjava');
        $this->post('/admin/prijava', ['email'=>$user->email, 'password'=>'existing-test-password'])->assertSessionHasErrors('email');
        $this->post('/admin/prijava', ['email'=>$user->email, 'password'=>'new-test-password'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_password_validation_keeps_existing_password_and_never_flashes_secrets(): void
    {
        $user = $this->user(); $originalHash = $user->password;
        $this->actingAs($user)->get('/admin/account');
        $cases = [
            [['current_password'=>'wrong-password'], 'current_password'],
            [['password_confirmation'=>'different-password'], 'password'],
            [['password'=>'short', 'password_confirmation'=>'short'], 'password'],
            [['password'=>'existing-test-password', 'password_confirmation'=>'existing-test-password'], 'password'],
        ];
        foreach ($cases as [$overrides, $error]) {
            $this->from('/admin/account')->put('/admin/account/password', array_merge($this->passwords(), $overrides))
                ->assertRedirect('/admin/account')->assertSessionHasErrors($error)
                ->assertSessionMissing('_old_input.current_password')->assertSessionMissing('_old_input.password')->assertSessionMissing('_old_input.password_confirmation');
            $this->assertSame($originalHash, $user->fresh()->password);
        }
    }

    public function test_only_master_can_manage_other_accounts_and_master_cannot_be_deleted_or_reset(): void
    {
        $master = $this->user('master@clinic.test', true); $editor = $this->user(); $other = $this->user('other@clinic.test');
        $this->get('/admin/account')->assertRedirect('/admin/prijava');
        $this->put('/admin/account/password', $this->passwords())->assertRedirect('/admin/prijava');
        $this->actingAs($editor)->get('/admin/users')->assertOk()->assertSee('Promijeni lozinku')->assertDontSee('Dodaj administratora')->assertDontSee('/delete');
        $this->get('/admin/users/create')->assertForbidden();
        $this->post('/admin/users', ['name'=>'Injected master', 'email'=>'injected@clinic.test', 'password'=>'test-password', 'password_confirmation'=>'test-password', 'is_master'=>true])->assertForbidden();
        foreach ([$master, $other] as $target) {
            $this->get('/admin/users/'.$target->id.'/password')->assertForbidden();
            $this->put('/admin/users/'.$target->id.'/password', $this->passwords())->assertForbidden();
            $this->get('/admin/users/'.$target->id.'/delete')->assertForbidden();
            $this->delete('/admin/users/'.$target->id, ['current_password'=>'existing-test-password'])->assertForbidden();
        }
        $this->withSession(['password_hash_web'=>null])->actingAs($master);
        $this->get('/admin/users')->assertSee('Master · zaštićen')->assertSee('/admin/users/'.$other->id.'/delete', false)->assertDontSee('/admin/users/'.$master->id.'/delete', false);
        $this->get('/admin/users/'.$master->id.'/password')->assertForbidden();
        $this->put('/admin/users/'.$master->id.'/password', $this->passwords())->assertForbidden();
        $this->get('/admin/users/'.$master->id.'/delete')->assertForbidden();
        $this->delete('/admin/users/'.$master->id, ['current_password'=>'existing-test-password'])->assertForbidden();
        $this->assertDatabaseHas('users', ['id'=>$master->id, 'is_master'=>true]);
        $this->post('/admin/users', ['name'=>'New admin', 'email'=>'new@clinic.test', 'password'=>'test-password', 'password_confirmation'=>'test-password', 'is_master'=>true])->assertRedirect('/admin/users');
        $this->assertFalse(User::where('email', 'new@clinic.test')->firstOrFail()->is_master);
    }

    public function test_master_password_reset_revokes_old_sessions_without_changing_master_password(): void
    {
        $master = $this->user('master@clinic.test', true); $editor = $this->user();
        $oldMasterHash = $master->password; $oldEditorHash = $editor->password;
        $this->actingAs($master)->get('/admin/users/'.$editor->id.'/password')->assertOk()->assertSee('Vaša master lozinka');
        $this->put('/admin/users/'.$editor->id.'/password', array_merge($this->passwords(), ['current_password'=>'wrong-password']))->assertSessionHasErrors('current_password');
        $this->assertSame($oldEditorHash, $editor->fresh()->password);
        $this->put('/admin/users/'.$editor->id.'/password', $this->passwords())->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-test-password', $editor->fresh()->password));
        $this->assertSame($oldMasterHash, $master->fresh()->password);
        $this->get('/admin')->assertOk();
        $this->actingAs($editor->fresh())->withSession(['password_hash_web'=>$oldEditorHash])->get('/admin')->assertRedirect('/admin/prijava');
        $this->assertGuest();
        $this->post('/admin/prijava', ['email'=>$editor->email, 'password'=>'new-test-password'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_master_deletes_another_user_only_after_password_confirmation_and_keeps_content(): void
    {
        $master = $this->user('master@clinic.test', true); $editor = $this->user();
        SiteContent::create(['key'=>'intro', 'value'=>['title'=>'Sačuvan sadržaj']]);
        $this->actingAs($master)->get('/admin/users/'.$editor->id.'/delete')->assertOk()->assertSee($editor->email)->assertSee('Obriši korisnika');
        $this->delete('/admin/users/'.$editor->id, ['current_password'=>'wrong-password'])->assertSessionHasErrors('current_password');
        $this->assertDatabaseHas('users', ['id'=>$editor->id]);
        $this->delete('/admin/users/'.$editor->id, ['current_password'=>'existing-test-password'])->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('users', ['id'=>$editor->id]);
        $this->assertDatabaseHas('users', ['id'=>$master->id]);
        $this->assertSame('Sačuvan sadržaj', SiteContent::section('intro')['title']);
        $this->post('/admin/odjava');
        $this->post('/admin/prijava', ['email'=>$editor->email, 'password'=>'existing-test-password'])->assertSessionHasErrors('email');
    }

    public function test_password_guesses_are_rate_limited_and_non_admins_cannot_use_account_form(): void
    {
        $reader = $this->user('reader@clinic.test', false, false);
        $this->actingAs($reader)->get('/admin/account')->assertForbidden();
        $this->put('/admin/account/password', $this->passwords())->assertForbidden();
        $user = $this->user();
        $this->withSession(['password_hash_web'=>null])->actingAs($user);
        for ($attempt=0; $attempt<5; $attempt++) {
            $this->put('/admin/account/password', array_merge($this->passwords(), ['current_password'=>'wrong-password']))->assertSessionHasErrors('current_password');
        }
        $this->put('/admin/account/password', $this->passwords())->assertStatus(429);
        $this->assertTrue(Hash::check('existing-test-password', $user->fresh()->password));
    }
}
