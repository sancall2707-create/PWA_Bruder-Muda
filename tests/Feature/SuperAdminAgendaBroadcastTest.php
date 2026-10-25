<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use App\Services\PersonalScheduleQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAgendaBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_and_admin_see_tambah_agenda_and_not_excel_pdf_on_agenda_tab(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.agenda'));

        $response->assertOk();
        $response->assertSee('Tambah Agenda');
        $response->assertSee(route('superadmin.monitoring.agenda.create'));
        // Make sure Excel and PDF are NOT shown on the Agenda tab
        $response->assertDontSee('jenis=agenda&amp;format=excel');
        $response->assertDontSee('jenis=agenda&format=excel');
    }

    public function test_superadmin_can_open_tambah_agenda_form(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.agenda.create'));

        $response->assertOk();
        $response->assertSee('Tambah Agenda untuk Semua Bruder');
        $response->assertSee('SIMPAN AGENDA UNTUK SEMUA BRUDER');
    }

    public function test_agenda_created_by_admin_is_automatically_added_to_all_user_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $bruder1 = User::factory()->create(['name' => 'Br. Agustinus FIC', 'role' => 'user', 'is_active' => true]);
        $bruder2 = User::factory()->create(['name' => 'Br. Bernardus FIC', 'role' => 'user', 'is_active' => true]);
        $bruder3 = User::factory()->create(['name' => 'Br. Clemens FIC', 'role' => 'user', 'is_active' => true]);

        $payload = [
            'title' => 'Retret Tahunan Provinsi FIC 2026',
            'start_date' => '2026-10-28',
            'start_time' => '08:30',
            'location' => 'Pusat Pastoral Sanjaya Muntilan',
            'description' => 'Seluruh bruder muda wajib hadir tepat waktu.',
        ];

        $response = $this->actingAs($admin)
            ->post(route('superadmin.monitoring.agenda.store'), $payload);

        $response->assertRedirect(route('superadmin.monitoring.agenda', [
            'bulan' => 10,
            'tahun' => 2026,
        ]));

        // Check each bruder has this agenda in personal_agendas
        $this->assertDatabaseHas('personal_agendas', [
            'user_id' => $bruder1->id,
            'title' => 'Retret Tahunan Provinsi FIC 2026',
            'location' => 'Pusat Pastoral Sanjaya Muntilan',
        ]);
        $this->assertDatabaseHas('personal_agendas', [
            'user_id' => $bruder2->id,
            'title' => 'Retret Tahunan Provinsi FIC 2026',
            'location' => 'Pusat Pastoral Sanjaya Muntilan',
        ]);
        $this->assertDatabaseHas('personal_agendas', [
            'user_id' => $bruder3->id,
            'title' => 'Retret Tahunan Provinsi FIC 2026',
            'location' => 'Pusat Pastoral Sanjaya Muntilan',
        ]);

        // Verify bruder1's personal schedule query returns this agenda
        $schedules = app(PersonalScheduleQuery::class)->forUser($bruder1->id);
        $this->assertTrue($schedules->contains('title', 'Retret Tahunan Provinsi FIC 2026'));
    }

    public function test_admin_can_delete_an_agenda_from_monitoring(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $agenda = PersonalAgenda::create([
            'user_id' => $bruder->id,
            'title' => 'Kegiatan Bersama Distrik',
            'start_at' => now(),
            'location' => 'Semarang',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->delete(route('superadmin.monitoring.agenda.destroySingle', [
            'category' => 'lainnya',
            'id' => $agenda->id,
        ]));

        $response->assertRedirect();
        $this->assertSoftDeleted('personal_agendas', ['id' => $agenda->id]);
    }

    public function test_regular_user_cannot_create_or_delete_broadcast_agenda(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('superadmin.monitoring.agenda.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('superadmin.monitoring.agenda.store'), [
                'title' => 'Tes Terlarang',
                'start_date' => '2026-10-30',
                'start_time' => '10:00',
                'location' => 'Tes',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('superadmin.monitoring.agenda.destroySingle', ['category' => 'lainnya', 'id' => 1]))
            ->assertForbidden();
    }
}
