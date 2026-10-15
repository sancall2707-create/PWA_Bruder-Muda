<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $this->get(route('profil.foto'))->assertRedirect(route('login'));
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

    public function test_user_uploads_replaces_and_keeps_profile_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sapaan/jangan-dihapus.jpg', $this->tinyJpeg());

        $user = User::factory()->create([
            'name' => 'Br. Foto',
            'username' => 'bruder_foto',
            'email' => 'foto@fic.local',
            'community_location' => 'Komunitas Asal',
            'role' => 'user',
            'profile_photo' => 'sapaan/jangan-dihapus.jpg',
        ]);
        $passwordHash = $user->password;

        $this->actingAs($user)
            ->get(route('profil.edit'))
            ->assertOk()
            ->assertSee('Tambahkan Foto Profil')
            ->assertDontSee('Ganti Foto Profil');

        $this->actingAs($user)
            ->get(route('profil'))
            ->assertOk()
            ->assertDontSee(route('profil.foto'), false)
            ->assertSee('/images/logo-fic.png', false);

        $this->actingAs($user)->get(route('profil.foto'))->assertNotFound();

        $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Foto',
            'email' => 'foto@fic.local',
            'community_location' => 'Komunitas Asal',
            'role' => 'super_admin',
            'profile_photo' => UploadedFile::fake()->createWithContent('pertama.jpg', $this->tinyJpeg()),
        ])->assertRedirect(route('profil'))
            ->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $user->refresh();
        $first = $user->profile_photo;
        $this->assertIsString($first);
        $this->assertStringStartsWith('profil/', $first);
        Storage::disk('public')->assertExists($first);
        Storage::disk('public')->assertExists('sapaan/jangan-dihapus.jpg');
        $this->assertSame('user', $user->role);
        $this->assertSame($passwordHash, $user->password);

        $this->actingAs($user)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee(route('profil.foto'), false)
            ->assertSee('/images/logo-fic.png', false)
            ->assertSee('alt="Foto profil Br. Foto"', false);

        $this->actingAs($user)
            ->get(route('profil.edit'))
            ->assertOk()
            ->assertSee('Ganti Foto Profil')
            ->assertDontSee('Tambahkan Foto Profil')
            ->assertSee('alt="Foto profil saat ini"', false)
            ->assertSee(route('profil.foto'), false);

        $owned = $this->actingAs($user)->get(route('profil.foto'));
        $owned->assertOk();
        $this->assertSame($this->tinyJpeg(), $owned->streamedContent());

        $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Foto',
            'email' => 'foto@fic.local',
            'community_location' => 'Komunitas Asal',
            'profile_photo' => UploadedFile::fake()->createWithContent('kedua.png', $this->tinyPng()),
        ])->assertRedirect(route('profil'));

        $user->refresh();
        $second = $user->profile_photo;
        $this->assertIsString($second);
        $this->assertNotSame($first, $second);
        $this->assertStringStartsWith('profil/', $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertExists('sapaan/jangan-dihapus.jpg');
        $this->assertSame($this->tinyPng(), Storage::disk('public')->get($second));

        $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Foto Tetap',
            'email' => 'foto@fic.local',
            'community_location' => 'Komunitas Tetap',
            'profile_photo' => '',
        ])->assertRedirect(route('profil'));

        $user->refresh();
        $this->assertSame($second, $user->profile_photo);
        $this->assertSame('Br. Foto Tetap', $user->name);
        $this->assertSame('Komunitas Tetap', $user->community_location);
        Storage::disk('public')->assertExists($second);
    }

    public function test_profile_photo_rejects_other_file_types_in_indonesian(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'name' => 'Br. Tolak',
            'email' => 'tolak@fic.local',
            'profile_photo' => null,
        ]);

        $this->actingAs($user)
            ->from(route('profil.edit'))
            ->post(route('profil.update'), [
                'name' => 'Nama Tidak Tersimpan',
                'email' => 'tolak@fic.local',
                'community_location' => 'Komunitas Tidak Tersimpan',
                'profile_photo' => UploadedFile::fake()->createWithContent('catatan.txt', 'bukan gambar'),
            ])
            ->assertRedirect(route('profil.edit'))
            ->assertSessionHasErrors('profile_photo');

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('profil.edit'))
            ->post(route('profil.update'), [
                'name' => 'Nama Tidak Tersimpan',
                'email' => 'tolak@fic.local',
                'community_location' => 'Komunitas Tidak Tersimpan',
                'profile_photo' => UploadedFile::fake()->createWithContent(
                    'animasi.gif',
                    base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')
                ),
            ])
            ->assertSee('Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.');

        $user->refresh();
        $this->assertSame('Br. Tolak', $user->name);
        $this->assertNull($user->profile_photo);
        $this->assertNull($user->community_location);
        Storage::disk('public')->assertDirectoryEmpty('profil');
    }

    public function test_another_user_cannot_change_or_fetch_someone_elses_photo(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create([
            'name' => 'Pemilik Foto',
            'username' => 'pemilik_foto',
            'email' => 'pemilik.foto@fic.local',
            'role' => 'user',
        ]);
        $other = User::factory()->create([
            'name' => 'Bruder Lain',
            'username' => 'bruder_lain_foto',
            'email' => 'lain.foto@fic.local',
            'role' => 'user',
        ]);

        $this->actingAs($owner)->post(route('profil.update'), [
            'name' => 'Pemilik Foto',
            'email' => 'pemilik.foto@fic.local',
            'community_location' => 'Komunitas Pemilik',
            'profile_photo' => UploadedFile::fake()->createWithContent('pemilik.jpg', $this->tinyJpeg()),
        ])->assertRedirect(route('profil'));

        $owner->refresh();
        $ownerPath = $owner->profile_photo;
        $this->assertIsString($ownerPath);

        $this->actingAs($other)->get(route('profil.foto'))->assertNotFound();

        $this->actingAs($other)->post(route('profil.update'), [
            'name' => 'Bruder Lain Berfoto',
            'email' => 'lain.foto@fic.local',
            'community_location' => 'Komunitas Lain',
            'id' => $owner->id,
            'username' => $owner->username,
            'profile_photo' => UploadedFile::fake()->createWithContent('lain.png', $this->tinyPng()),
        ])->assertRedirect(route('profil'));

        $owner->refresh();
        $other->refresh();

        $this->assertSame($ownerPath, $owner->profile_photo);
        $this->assertSame('Pemilik Foto', $owner->name);
        $this->assertSame('user', $owner->role);
        Storage::disk('public')->assertExists($ownerPath);

        $this->assertIsString($other->profile_photo);
        $this->assertNotSame($ownerPath, $other->profile_photo);
        $this->assertStringStartsWith('profil/', $other->profile_photo);
        $this->assertSame('Bruder Lain Berfoto', $other->name);

        $response = $this->actingAs($other)->get(route('profil.foto'));
        $response->assertOk();
        $this->assertSame($this->tinyPng(), $response->streamedContent());
        $this->assertNotSame($this->tinyJpeg(), $response->streamedContent());
        $this->assertSame($this->tinyJpeg(), Storage::disk('public')->get($ownerPath));
    }

    public function test_webp_profile_photo_is_accepted_and_traversal_path_is_not_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sapaan/jangan-dihapus.jpg', $this->tinyJpeg());

        $user = User::factory()->create([
            'name' => 'Br. Webp',
            'email' => 'webp@fic.local',
            'profile_photo' => 'profil/../sapaan/jangan-dihapus.jpg',
        ]);

        $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Webp',
            'email' => 'webp@fic.local',
            'community_location' => 'Komunitas Webp',
            'profile_photo' => UploadedFile::fake()->createWithContent('foto.webp', $this->tinyWebp()),
        ])->assertRedirect(route('profil'));

        $user->refresh();
        $this->assertIsString($user->profile_photo);
        $this->assertStringStartsWith('profil/', $user->profile_photo);
        $this->assertStringNotContainsString('..', $user->profile_photo);
        Storage::disk('public')->assertExists('sapaan/jangan-dihapus.jpg');
        $this->assertSame($this->tinyWebp(), Storage::disk('public')->get($user->profile_photo));

        $this->actingAs($user)->get(route('profil.foto'))->assertOk();
    }

    private function tinyJpeg(): string
    {
        return base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
    }

    private function tinyPng(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGPgEpEDAABoAD1UCKP3AAAAAElFTkSuQmCC');
    }

    private function tinyWebp(): string
    {
        return base64_decode('UklGRi4AAABXRUJQVlA4ICIAAABwAQCdASoBAAEAAUAmJZQCdAFAAAD+/DeBV/fU6D4r4AAA');
    }
}
