<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_dashboard_counts_saved_reports(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Uji Kepatuhan',
            'community_location' => 'Komunitas Uji',
            'is_active' => true,
        ]);
        $this->publishJournal($bruder, 'Jurnal Kepatuhan');

        $this->actingAs($admin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('33%')
            ->assertSee('1 dari 3 laporan selesai')
            ->assertSee('Br. Uji Kepatuhan')
            ->assertSee('Komunitas Uji')
            ->assertSee('1/3 bulan ini')
            ->assertSee('Aktif')
            ->assertSee('Kelola Data Bruder')
            ->assertSee('Monitoring & Ekspor Data')
            ->assertSee('Buat Sapaan Provinsial')
            ->assertSee('aria-label="Buka menu"', false)
            ->assertSee(route('logout'), false)
            ->assertSee('sa-bottom-nav', false);
    }

    public function test_bruder_and_guest_cannot_open_account_management(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->get(route('superadmin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs($bruder)->get(route('superadmin.dashboard'))->assertForbidden();
        $this->actingAs($bruder)->get(route('superadmin.users.index'))->assertForbidden();
        $this->actingAs($bruder)->post(route('superadmin.users.store'), [
            'name' => 'Br. Gelap',
            'username' => 'gelap',
            'password' => 'rahasia-aman',
        ])->assertForbidden();
        $this->actingAs($bruder)->get(route('superadmin.users.account.edit', $bruder))->assertForbidden();
        $this->actingAs($bruder)->post(route('superadmin.users.account.update', $bruder), [
            'email' => 'gelap@fic.test',
            'password' => 'sandi-baru-aman',
            'password_confirmation' => 'sandi-baru-aman',
        ])->assertForbidden();
    }

    public function test_super_admin_creates_an_account_that_can_log_in(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->post(route('superadmin.users.store'), [
            'name' => 'Br. Baru Uji',
            'username' => 'Bruder_Baru',
            'password' => 'rahasia-aman',
            'community_location' => 'Komunitas Ambarawa',
        ])->assertRedirect(route('superadmin.users.index'));

        $created = User::where('username', 'bruder_baru')->first();
        $this->assertNotNull($created);
        $this->assertSame('user', $created->role);
        $this->assertTrue($created->is_active);
        $this->assertTrue(Hash::check('rahasia-aman', $created->password));

        auth()->logout();

        $this->post(route('login.store'), [
            'username' => 'bruder_baru',
            'password' => 'rahasia-aman',
        ])->assertRedirect('/sapaan-provinsial');
    }

    public function test_search_matches_community_and_hides_other_bruders(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Ambarawa',
            'community_location' => 'Komunitas Ambarawa',
        ]);
        User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Yogyakarta',
            'community_location' => 'Komunitas Yogyakarta',
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.users.index', ['search' => 'Ambarawa']))
            ->assertOk()
            ->assertSee('Br. Ambarawa')
            ->assertDontSee('Br. Yogyakarta');
    }

    public function test_status_and_delete_keep_the_saved_journal(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Status Uji',
            'is_active' => true,
        ]);
        $journal = $this->publishJournal($bruder, 'Jurnal Tetap Ada');

        $this->actingAs($admin)
            ->post(route('superadmin.users.status', $bruder))
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertFalse($bruder->fresh()->is_active);
        $this->actingAs($admin)
            ->get(route('superadmin.dashboard'))
            ->assertSee('0 dari 0 laporan selesai')
            ->assertSee('Nonaktif');

        $this->actingAs($admin)
            ->delete(route('superadmin.users.destroy', $bruder))
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $bruder->id]);
        $this->assertDatabaseHas('self_development_journals', ['id' => $journal->id]);
        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertSee('dihapus dari daftar')
            ->assertDontSee('@'.$bruder->username);
    }

    public function test_super_admin_can_change_email_without_touching_password(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Email Uji',
            'username' => 'bruder_email',
            'email' => 'lama@fic.test',
            'password' => 'sandi-lama-aman',
        ]);
        $hash = $bruder->password;

        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->assertSee('Kelola Akun');

        $this->actingAs($admin)
            ->get(route('superadmin.users.account.edit', $bruder))
            ->assertOk()
            ->assertSee('Br. Email Uji')
            ->assertSee('bruder_email')
            ->assertSee('lama@fic.test')
            ->assertSee('Kelola Akun Bruder')
            ->assertDontSee($hash);

        $this->actingAs($admin)
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'baru@fic.test',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('success');

        $fresh = $bruder->fresh();
        $this->assertSame('baru@fic.test', $fresh->email);
        $this->assertSame($hash, $fresh->password);
        $this->assertTrue(Hash::check('sandi-lama-aman', $fresh->password));
        $this->assertSame('bruder_email', $fresh->username);
        $this->assertSame('Br. Email Uji', $fresh->name);
        $this->assertSame('user', $fresh->role);

        $this->actingAs($admin)
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => '',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertNull($bruder->fresh()->email);
        $this->assertSame($hash, $bruder->fresh()->password);
    }

    public function test_account_update_rejects_email_owned_by_another_account(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $owner = User::factory()->create([
            'role' => 'user',
            'email' => 'milik@fic.test',
        ]);
        $bruder = User::factory()->create([
            'role' => 'user',
            'email' => 'sendiri@fic.test',
            'password' => 'sandi-lama-aman',
        ]);
        $hash = $bruder->password;

        $this->actingAs($admin)
            ->from(route('superadmin.users.account.edit', $bruder))
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'Milik@fic.test',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('superadmin.users.account.edit', $bruder))
            ->assertSessionHasErrors('email');

        $fresh = $bruder->fresh();
        $this->assertSame('sendiri@fic.test', $fresh->email);
        $this->assertSame($hash, $fresh->password);
        $this->assertSame('milik@fic.test', $owner->fresh()->email);

        $this->actingAs($admin)
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'sendiri@fic.test',
                'username' => 'dicuri',
                'role' => 'super_admin',
                'name' => 'Nama Diubah',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('superadmin.users.index'));

        $kept = $bruder->fresh();
        $this->assertSame('sendiri@fic.test', $kept->email);
        $this->assertSame($bruder->username, $kept->username);
        $this->assertSame('user', $kept->role);
        $this->assertSame($bruder->name, $kept->name);
    }

    public function test_password_reset_replaces_the_old_login(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'username' => 'bruder_sandi',
            'email' => 'sandi@fic.test',
            'password' => 'sandi-lama-aman',
        ]);

        $this->actingAs($admin)
            ->from(route('superadmin.users.account.edit', $bruder))
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'sandi@fic.test',
                'password' => 'pendek',
                'password_confirmation' => 'pendek',
            ])
            ->assertRedirect(route('superadmin.users.account.edit', $bruder))
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('sandi-lama-aman', $bruder->fresh()->password));

        $this->actingAs($admin)
            ->from(route('superadmin.users.account.edit', $bruder))
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'sandi@fic.test',
                'password' => 'sandi-baru-aman',
                'password_confirmation' => 'tidak-sama-aman',
            ])
            ->assertRedirect(route('superadmin.users.account.edit', $bruder))
            ->assertSessionHasErrors('password');

        $this->actingAs($admin)
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'sandi.baru@fic.test',
                'password' => 'sandi-baru-aman',
                'password_confirmation' => 'sandi-baru-aman',
            ])
            ->assertRedirect(route('superadmin.users.index'));

        $fresh = $bruder->fresh();
        $this->assertSame('sandi.baru@fic.test', $fresh->email);
        $this->assertFalse(Hash::check('sandi-lama-aman', $fresh->password));
        $this->assertTrue(Hash::check('sandi-baru-aman', $fresh->password));

        auth()->logout();

        $this->post(route('login.store'), [
            'username' => 'bruder_sandi',
            'password' => 'sandi-lama-aman',
        ])->assertSessionHasErrors('username');

        $this->post(route('login.store'), [
            'username' => 'bruder_sandi',
            'password' => 'sandi-baru-aman',
        ])->assertRedirect('/sapaan-provinsial');
    }

    public function test_admin_cannot_open_or_submit_account_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'email' => 'tetap@fic.test',
            'password' => 'sandi-lama-aman',
        ]);
        $hash = $bruder->password;

        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->assertDontSee('Kelola Akun');

        $this->actingAs($admin)
            ->get(route('superadmin.users.account.edit', $bruder))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('superadmin.users.account.update', $bruder), [
                'email' => 'dicuri@fic.test',
                'password' => 'sandi-baru-aman',
                'password_confirmation' => 'sandi-baru-aman',
            ])
            ->assertForbidden();

        $fresh = $bruder->fresh();
        $this->assertSame('tetap@fic.test', $fresh->email);
        $this->assertSame($hash, $fresh->password);

        $super = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($super)
            ->get(route('superadmin.users.account.edit', $admin))
            ->assertOk();
    }

    public function test_super_admin_sees_admins_in_user_directory_without_track_record(): void
    {
        $super = User::factory()->create(['role' => 'super_admin', 'name' => 'Super Admin Uji']);
        $ketua = User::factory()->create(['role' => 'admin_ketua_tppbm', 'name' => 'Br. Ketua TPPBM Uji']);
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Biasa Uji']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Bruder Muda Uji']);

        $response = $this->actingAs($super)->get(route('superadmin.users.index'));
        $response->assertOk()
            ->assertSee('Br. Ketua TPPBM Uji')
            ->assertSee('Admin Biasa Uji')
            ->assertSee('Bruder Muda Uji')
            ->assertSee('Admin Ketua TPPBM')
            ->assertSee('Admin TPPBM')
            ->assertSee('Track Record'); // Untuk bruder muda ada Track Record

        // Track record untuk admin harus 404
        $this->actingAs($super)->get(route('superadmin.users.track-record', $ketua))->assertNotFound();
        $this->actingAs($super)->get(route('superadmin.users.track-record', $admin))->assertNotFound();
    }

    public function test_super_admin_can_upload_and_delete_user_photo_in_account_management(): void
    {
        Storage::fake('public');

        $super = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Uji Foto']);

        $photo = UploadedFile::fake()->image('bruder_foto.jpg', 600, 600);

        $response = $this->actingAs($super)->post(route('superadmin.users.account.update', $bruder), [
            'name' => 'Br. Uji Foto',
            'profile_photo' => $photo,
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $bruder->refresh();
        $this->assertNotNull($bruder->profile_photo);
        $this->assertStringStartsWith('profil/', $bruder->profile_photo);
        Storage::disk('public')->assertExists($bruder->profile_photo);

        // Akses route foto
        $photoResp = $this->actingAs($super)->get(route('superadmin.users.photo', $bruder));
        $photoResp->assertOk();

        // Hapus foto
        $this->actingAs($super)->post(route('superadmin.users.account.update', $bruder), [
            'name' => 'Br. Uji Foto',
            'remove_photo' => '1',
        ])->assertRedirect(route('superadmin.users.index'));

        $bruder->refresh();
        $this->assertNull($bruder->profile_photo);
    }

    public function test_track_record_shows_every_saved_report_and_rejects_other_roles(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'name' => 'Bukan Bruder']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Jejak']);

        foreach (range(1, 6) as $number) {
            $this->publishJournal($bruder, 'Jurnal '.$number);
        }

        $this->actingAs($admin)
            ->get(route('superadmin.users.track-record', $bruder))
            ->assertOk()
            ->assertSee('6 Jurnal')
            ->assertSee('Jurnal 6')
            ->assertSee('Br. Jejak');

        $this->actingAs($admin)
            ->get(route('superadmin.users.track-record', $admin))
            ->assertNotFound();
    }

    private function publishJournal(User $user, string $name): SelfDevelopmentJournal
    {
        return SelfDevelopmentJournal::create([
            'user_id' => $user->id,
            'activity_date' => now()->toDateString(),
            'activity_name' => $name,
            'organizer' => 'Dewan Provinsi',
            'speaker' => 'Rm. Uji',
            'description' => 'Catatan uji.',
            'reflection' => str_repeat('Refleksi pembinaan. ', 12),
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
