<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
            ->assertSee('Aktif');
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
