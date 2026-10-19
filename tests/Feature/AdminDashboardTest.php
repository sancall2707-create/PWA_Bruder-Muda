<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_be_accessed_by_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Br. Pendamping FIC',
            'community_location' => 'Komunitas Pusat Semarang',
        ]);

        $bruder = User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Antonius FIC',
            'community_location' => 'Komunitas Deltamas',
            'is_active' => true,
        ]);

        SpiritualGuidanceReport::query()->create([
            'user_id' => $bruder->id,
            'mentor_name' => 'Rm. Yohanes Pr',
            'meeting_at' => now(),
            'location' => 'Kapel Pusat',
            'report_content' => str_repeat('Refleksi bimbingan rohani bulanan. ', 10),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard TPPBM')
            ->assertSee('TIM PENDAMPING TPPBM')
            ->assertSee('Br. Pendamping FIC')
            ->assertSee('Br. Antonius FIC')
            ->assertSee('Monitoring & Ekspor Laporan')
            ->assertSee('Rekapitulasi Agenda & Kalender')
            ->assertSee('Direktori & Rekam Jejak Bruder')
            ->assertSee('Rilis Sapaan & Pengumuman Bersama')
            ->assertSee('Lihat PWA')
            ->assertSee('aria-label="Buka menu"', false)
            ->assertSee(route('logout'), false)
            ->assertDontSee('class="sa-logout"', false)
            ->assertDontSee('sa-bottom-nav', false)
            ->assertSee('Bimbingan Rohani');
    }

    public function test_admin_can_access_monitoring_and_sapaan_and_users_directory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Fransiskus FIC']);

        $this->actingAs($admin)->get(route('superadmin.monitoring.index'))->assertOk();
        $this->actingAs($admin)->get(route('superadmin.monitoring.agenda'))->assertOk();
        $this->actingAs($admin)->get(route('superadmin.sapaan.index'))->assertOk();
        $this->actingAs($admin)->get(route('superadmin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('superadmin.users.track-record', $bruder))->assertOk();
    }

    public function test_admin_cannot_access_super_admin_account_creation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('superadmin.users.create'))->assertForbidden();
        $this->actingAs($admin)->post(route('superadmin.users.store'), [
            'name' => 'Bruder Baru',
            'username' => 'bruder_baru',
            'password' => 'password123',
        ])->assertForbidden();

        $bruder = User::factory()->create(['role' => 'user']);
        $this->actingAs($admin)->get(route('superadmin.users.account.edit', $bruder))->assertForbidden();
        $this->actingAs($admin)->post(route('superadmin.users.account.update', $bruder), [
            'email' => 'admin-ubah@fic.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertForbidden();
    }

    public function test_bruder_cannot_access_admin_dashboard(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_sees_return_to_tppbm_dashboard_from_pwa(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kembali ke Dashboard TPPBM')
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_bruder_does_not_see_tppbm_return_link(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Kembali ke Dashboard TPPBM');
    }
}
