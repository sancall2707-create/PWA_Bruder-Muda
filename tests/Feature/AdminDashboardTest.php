<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
            ->assertSee('Beranda TPPBM')
            ->assertSee('TIM PENDAMPING TPPBM')
            ->assertSee('Br. Pendamping FIC')
            ->assertSee('Br. Antonius FIC')
            ->assertSee('Monitoring & Ekspor Laporan')
            ->assertSee('Rekapitulasi Agenda & Kalender')
            ->assertSee('Jadwal Pembinaan & Agenda Terdekat')
            ->assertSee('Direktori & Rekam Jejak Bruder')
            ->assertSee('Rilis Sapaan & Pengumuman Bersama')
            ->assertSee('Beranda Bruder Muda')
            ->assertSee('aria-label="Buka menu"', false)
            ->assertSee(route('logout'), false)
            ->assertDontSee('class="sa-logout"', false)
            ->assertSee('sa-bottom-nav', false)
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

    public function test_admin_ketua_tppbm_can_access_admin_dashboard(): void
    {
        $ketua = User::factory()->create([
            'role' => 'admin_ketua_tppbm',
            'name' => 'Br. F.A. Galih Sih Hartanta, FIC',
        ]);

        $this->actingAs($ketua)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Beranda TPPBM')
            ->assertSee('KETUA TPPBM');

        $this->actingAs($ketua)->get(route('superadmin.monitoring.raport'))->assertOk();
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
            ->assertSee('Kembali ke Beranda TPPBM')
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_bruder_does_not_see_tppbm_return_link(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Kembali ke Beranda TPPBM');
    }

    public function test_admin_and_super_admin_dashboards_show_aggregated_formation_rings(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-24 10:00:00', 'Asia/Jakarta'));

        try {
            $admin = User::factory()->create(['role' => 'admin']);
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $bruderTahun = User::factory()->create(['role' => 'user', 'is_active' => true, 'name' => 'Br. Tahunan']);
            $bruderBulan = User::factory()->create(['role' => 'user', 'is_active' => true, 'name' => 'Br. Bulanan']);
            $bruderNonaktif = User::factory()->create(['role' => 'user', 'is_active' => false, 'name' => 'Br. Nonaktif']);

            SpiritualGuidanceReport::query()->create([
                'user_id' => $bruderTahun->id,
                'mentor_name' => 'Rm. Januari',
                'meeting_at' => Carbon::parse('2026-01-15 09:00:00', 'Asia/Jakarta'),
                'location' => 'Kapel',
                'report_content' => 'Bimbingan Januari yang hanya masuk akumulasi tahunan.',
                'status' => 'submitted',
                'submitted_at' => Carbon::parse('2026-01-15 09:00:00', 'Asia/Jakarta'),
            ]);

            SpiritualGuidanceReport::query()->create([
                'user_id' => $bruderTahun->id,
                'mentor_name' => 'Rm. Draf',
                'meeting_at' => Carbon::parse('2026-10-10 09:00:00', 'Asia/Jakarta'),
                'location' => 'Kapel',
                'report_content' => 'Draf tidak dihitung.',
                'status' => 'draft',
            ]);

            SpiritualGuidanceReport::query()->create([
                'user_id' => $bruderTahun->id,
                'mentor_name' => 'Rm. Lama',
                'meeting_at' => Carbon::parse('2025-12-20 09:00:00', 'Asia/Jakarta'),
                'location' => 'Kapel',
                'report_content' => 'Laporan tahun lalu tidak dihitung.',
                'status' => 'submitted',
                'submitted_at' => Carbon::parse('2025-12-20 09:00:00', 'Asia/Jakarta'),
            ]);

            WawanhatiReport::query()->create([
                'user_id' => $bruderBulan->id,
                'local_leader_name' => 'Br. Pimpinan',
                'meeting_at' => Carbon::parse('2026-10-10 09:00:00', 'Asia/Jakarta'),
                'location' => 'Komunitas',
                'report_content' => 'Wawanhati bulan berjalan ikut akumulasi tahunan.',
                'status' => 'submitted',
                'submitted_at' => Carbon::parse('2026-10-10 09:00:00', 'Asia/Jakarta'),
            ]);

            SpiritualGuidanceReport::query()->create([
                'user_id' => $bruderNonaktif->id,
                'mentor_name' => 'Rm. Nonaktif',
                'meeting_at' => Carbon::parse('2026-10-12 09:00:00', 'Asia/Jakarta'),
                'location' => 'Kapel',
                'report_content' => 'Laporan bruder nonaktif tidak masuk agregat.',
                'status' => 'submitted',
                'submitted_at' => Carbon::parse('2026-10-12 09:00:00', 'Asia/Jakarta'),
            ]);

            SelfDevelopmentJournal::query()->create([
                'user_id' => $bruderBulan->id,
                'activity_date' => '2026-10-11',
                'activity_name' => 'Lokakarya',
                'description' => 'Jurnal tidak masuk target 2/24.',
                'reflection' => 'Refleksi jurnal tidak dihitung sebagai sesi wajib.',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-11 09:00:00', 'Asia/Jakarta'),
            ]);

            $label = 'Akumulasi Januari – '.now('Asia/Jakarta')->locale('id')->translatedFormat('F Y');

            foreach ([
                route('admin.dashboard') => $admin,
                route('superadmin.dashboard') => $superAdmin,
            ] as $url => $actor) {
                $this->actingAs($actor)->get($url)
                    ->assertOk()
                    ->assertSee('Akumulasi Tahunan')
                    ->assertSee('Bulan Berjalan')
                    ->assertSee('stat-year-summary', false)
                    ->assertSee('progress-stat-card', false)
                    ->assertSee('stroke-dasharray:201', false)
                    ->assertSee($label, false)
                    ->assertSee('2/48')
                    ->assertSee('1/4')
                    ->assertSee('2 / 48', false)
                    ->assertSee('4%')
                    ->assertSee('25%')
                    ->assertDontSee('Tingkat Kepatuhan Laporan')
                    ->assertDontSee('Persentase Kepatuhan');
            }
        } finally {
            Carbon::setTestNow();
        }
    }
}
