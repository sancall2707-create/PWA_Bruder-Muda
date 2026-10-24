<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\User;
use App\Models\WawanhatiReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminMonitoringReportDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_sees_single_delete_and_bulk_delete_tools_in_monitoring(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder = User::factory()->create(['name' => 'Br. Uji FIC', 'role' => 'user', 'is_active' => true]);

        SpiritualGuidanceReport::create([
            'user_id' => $bruder->id,
            'mentor_name' => 'Br. Mentor',
            'meeting_at' => now(),
            'location' => 'Ambarawa',
            'report_content' => str_repeat('Bimbingan ', 25),
            'status' => 'submitted',
        ]);

        $res = $this->actingAs($admin)->get(route('superadmin.monitoring.index'));

        $res->assertOk();
        $res->assertSee('checkAllReports');
        $res->assertSee('btnHapusReports');
        $res->assertSee('modalLaporan');
        $res->assertSee('konfirmasiHapusSatuLaporan');
        $res->assertSee('konfirmasiHapusMassalLaporan');
    }

    public function test_superadmin_can_delete_single_report_from_monitoring(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $rep = SpiritualGuidanceReport::create([
            'user_id' => $bruder->id,
            'mentor_name' => 'Br. Mentor',
            'meeting_at' => now(),
            'location' => 'Ambarawa',
            'report_content' => str_repeat('Bimbingan ', 25),
            'status' => 'submitted',
        ]);

        $this->actingAs($admin)
            ->delete(route('superadmin.monitoring.laporan.destroySingle', [
                'type' => 'bimbingan',
                'id' => $rep->id,
            ]))
            ->assertRedirect();

        $this->assertSoftDeleted('spiritual_guidance_reports', ['id' => $rep->id]);
    }

    public function test_superadmin_can_bulk_delete_reports_of_mixed_types(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $bimbingan = SpiritualGuidanceReport::create([
            'user_id' => $bruder->id,
            'mentor_name' => 'Br. Mentor',
            'meeting_at' => now(),
            'location' => 'Ambarawa',
            'report_content' => str_repeat('Bimbingan ', 25),
            'status' => 'submitted',
        ]);

        $wawanhati = WawanhatiReport::create([
            'user_id' => $bruder->id,
            'local_leader_name' => 'Br. Piko',
            'meeting_at' => now(),
            'location' => 'Semarang',
            'report_content' => str_repeat('Wawanhati ', 25),
            'status' => 'submitted',
        ]);

        $jurnal = SelfDevelopmentJournal::create([
            'user_id' => $bruder->id,
            'activity_date' => now()->toDateString(),
            'activity_name' => 'Jurnal Alpha',
            'organizer' => 'FIC',
            'speaker' => 'X',
            'description' => 'Desc',
            'reflection' => str_repeat('Refleksi ', 30),
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('superadmin.monitoring.laporan.destroyBulk'), [
                'items' => [
                    "bimbingan:{$bimbingan->id}",
                    "wawanhati:{$wawanhati->id}",
                    "jurnal:{$jurnal->id}",
                ],
            ])
            ->assertRedirect();

        $this->assertSoftDeleted('spiritual_guidance_reports', ['id' => $bimbingan->id]);
        $this->assertSoftDeleted('wawanhati_reports', ['id' => $wawanhati->id]);
        $this->assertSoftDeleted('self_development_journals', ['id' => $jurnal->id]);
    }

    public function test_regular_user_cannot_delete_reports_via_monitoring(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->delete(route('superadmin.monitoring.laporan.destroySingle', [
                'type' => 'bimbingan',
                'id' => 1,
            ]))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('superadmin.monitoring.laporan.destroyBulk'), [
                'items' => ['bimbingan:1'],
            ])
            ->assertForbidden();
    }
}
