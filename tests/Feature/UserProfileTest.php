<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_updates_own_name_and_community_without_changing_role(): void
    {
        $user = User::factory()->create([
            'name' => 'Br. Pemilik',
            'username' => 'pemilik',
            'email' => 'pemilik@fic.local',
            'community_location' => 'Komunitas Asal',
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Pemilik Diperbarui',
            'community_location' => 'Komunitas Uji Profil',
            'email' => 'pemilik@fic.local',
            'username' => 'diretas',
            'role' => 'super_admin',
            'is_active' => false,
            'id' => 999,
        ]);

        $response->assertRedirect(route('profil'));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $user->refresh();
        $this->assertSame('Br. Pemilik Diperbarui', $user->name);
        $this->assertSame('Komunitas Uji Profil', $user->community_location);
        $this->assertSame('pemilik@fic.local', $user->email);
        $this->assertSame('pemilik', $user->username);
        $this->assertSame('user', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotSame(999, $user->id);

        $this->actingAs($user)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee('Br. Pemilik Diperbarui')
            ->assertSee('Komunitas Uji Profil')
            ->assertSee('Profil berhasil diperbarui.')
            ->assertSee(route('profil.edit'), false)
            ->assertSee(route('profil.kata-sandi'), false)
            ->assertDontSee('Tahap Lanjutan');
    }

    public function test_profile_validation_errors_are_shown(): void
    {
        $user = User::factory()->create([
            'email' => 'punya@fic.local',
        ]);
        User::factory()->create([
            'email' => 'terpakai@fic.local',
        ]);

        $this->actingAs($user)
            ->from(route('profil.edit'))
            ->post(route('profil.update'), [
                'name' => '',
                'community_location' => 'Komunitas Uji',
                'email' => 'bukan-email',
            ])
            ->assertRedirect(route('profil.edit'))
            ->assertSessionHasErrors(['name', 'email']);

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('profil.edit'))
            ->post(route('profil.update'), [
                'name' => '',
                'email' => 'terpakai@fic.local',
            ])
            ->assertSee('Nama wajib diisi.')
            ->assertSee('Email sudah digunakan akun lain.');

        $user->refresh();
        $this->assertNotSame('', $user->name);
        $this->assertSame('punya@fic.local', $user->email);
    }

    public function test_another_user_cannot_change_someone_elses_profile(): void
    {
        $owner = User::factory()->create([
            'name' => 'Pemilik Profil',
            'username' => 'pemilik_profil',
            'email' => 'pemilik.profil@fic.local',
            'community_location' => 'Komunitas Asal',
            'role' => 'user',
            'is_active' => true,
        ]);
        $other = User::factory()->create([
            'name' => 'Bruder Lain',
            'username' => 'bruder_lain',
            'email' => 'lain@fic.local',
            'community_location' => 'Komunitas Lain',
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->actingAs($other)->post(route('profil.update'), [
            'name' => 'Nama Disusupi',
            'community_location' => 'Komunitas Disusupi',
            'email' => 'lain@fic.local',
            'id' => $owner->id,
            'username' => $owner->username,
            'role' => 'super_admin',
            'is_active' => false,
        ])->assertRedirect(route('profil'));

        $owner->refresh();
        $other->refresh();

        $this->assertSame('Pemilik Profil', $owner->name);
        $this->assertSame('Komunitas Asal', $owner->community_location);
        $this->assertSame('pemilik_profil', $owner->username);
        $this->assertSame('user', $owner->role);
        $this->assertTrue($owner->is_active);

        $this->assertSame('Nama Disusupi', $other->name);
        $this->assertSame('Komunitas Disusupi', $other->community_location);
        $this->assertSame('bruder_lain', $other->username);
        $this->assertSame('user', $other->role);
        $this->assertTrue($other->is_active);
        $this->assertNotSame($owner->id, $other->id);
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create();
        $hash = $user->password;

        $this->actingAs($user)
            ->from(route('profil.kata-sandi'))
            ->post(route('profil.kata-sandi.update'), [
                'current_password' => 'sandi-salah',
                'password' => 'SandiBaru#2026',
                'password_confirmation' => 'SandiBaru#2026',
            ])
            ->assertRedirect(route('profil.kata-sandi'))
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('profil.kata-sandi'))
            ->post(route('profil.kata-sandi.update'), [
                'current_password' => 'sandi-salah',
                'password' => 'SandiBaru#2026',
                'password_confirmation' => 'SandiBaru#2026',
            ])
            ->assertSee('Kata sandi saat ini tidak sesuai.');

        $this->assertAuthenticatedAs($user);
        $this->assertSame($hash, $user->fresh()->password);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_password_must_differ_and_be_confirmed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profil.kata-sandi.update'), [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        $this->actingAs($user)->post(route('profil.kata-sandi.update'), [
            'current_password' => 'password',
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ])->assertSessionHasErrors('password');

        $this->actingAs($user)->post(route('profil.kata-sandi.update'), [
            'current_password' => 'password',
            'password' => 'SandiBaru#2026',
            'password_confirmation' => 'SandiLain#2026',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_valid_new_password_keeps_session_and_is_used_on_next_login(): void
    {
        $user = User::factory()->create([
            'username' => 'bruder_sandi',
            'role' => 'user',
        ]);
        $newPassword = 'SandiBaru#2026';

        $this->actingAs($user)
            ->post(route('profil.kata-sandi.update'), [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ])
            ->assertRedirect(route('profil'))
            ->assertSessionHas('success', 'Kata sandi berhasil diubah. Anda tetap masuk.');

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
        $this->assertFalse(Hash::check('password', $user->fresh()->password));
        $this->assertSame('user', $user->fresh()->role);

        auth()->logout();

        $this->post(route('login.store'), [
            'username' => 'bruder_sandi',
            'password' => 'password',
        ])->assertSessionHasErrors('username');

        $this->post(route('login.store'), [
            'username' => 'bruder_sandi',
            'password' => $newPassword,
        ])->assertRedirect('/sapaan-provinsial');

        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_cannot_open_profile_forms(): void
    {
        $this->get(route('profil.edit'))->assertRedirect(route('login'));
        $this->get(route('profil.kata-sandi'))->assertRedirect(route('login'));
        $this->post(route('profil.update'), [
            'name' => 'Tamu',
            'community_location' => 'Komunitas Tamu',
        ])->assertRedirect(route('login'));
        $this->post(route('profil.kata-sandi.update'), [
            'current_password' => 'password',
            'password' => 'SandiBaru#2026',
            'password_confirmation' => 'SandiBaru#2026',
        ])->assertRedirect(route('login'));
    }
}
