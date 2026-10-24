<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminJurnalMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_and_admin_can_view_all_bruder_journals(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder1 = User::factory()->create(['name' => 'Br. Alpha FIC', 'role' => 'user', 'is_active' => true]);
        $bruder2 = User::factory()->create(['name' => 'Br. Beta FIC', 'role' => 'user', 'is_active' => true]);

        SelfDevelopmentJournal::create([
            'user_id' => $bruder1->id, 'activity_date' => '2026-10-01',
            'activity_name' => 'Retret Alpha', 'organizer' => 'FIC', 'speaker' => 'Rm. X',
            'description' => 'Desc A', 'reflection' => str_repeat('Refleksi ', 30),
            'status' => 'published', 'published_at' => now(),
        ]);

        SelfDevelopmentJournal::create([
            'user_id' => $bruder2->id, 'activity_date' => '2026-10-02',
            'activity_name' => 'Retret Beta', 'organizer' => 'FIC', 'speaker' => 'Rm. Y',
            'description' => 'Desc B', 'reflection' => str_repeat('Refleksi ', 30),
            'status' => 'published', 'published_at' => now(),
        ]);

        $res = $this->actingAs($admin)->get(route('superadmin.monitoring.jurnal.index'));

        $res->assertOk();
        $res->assertSee('Retret Alpha');
        $res->assertSee('Retret Beta');
        $res->assertSee('Br. Alpha FIC');
        $res->assertSee('Br. Beta FIC');
        $res->assertSee('checkAllSA');
        $res->assertSee('btnHapusSA');
        $res->assertSee('modalSA');
    }

    public function test_superadmin_can_filter_journals_by_bruder(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder1 = User::factory()->create(['name' => 'Br. Alpha FIC', 'role' => 'user', 'is_active' => true]);
        $bruder2 = User::factory()->create(['name' => 'Br. Beta FIC', 'role' => 'user', 'is_active' => true]);

        SelfDevelopmentJournal::create([
            'user_id' => $bruder1->id, 'activity_date' => '2026-10-01',
            'activity_name' => 'Retret Alpha', 'organizer' => 'FIC', 'speaker' => 'Rm. X',
            'description' => 'Desc A', 'reflection' => str_repeat('Refleksi ', 30),
            'status' => 'published', 'published_at' => now(),
        ]);

        SelfDevelopmentJournal::create([
            'user_id' => $bruder2->id, 'activity_date' => '2026-10-02',
            'activity_name' => 'Retret Beta', 'organizer' => 'FIC', 'speaker' => 'Rm. Y',
            'description' => 'Desc B', 'reflection' => str_repeat('Refleksi ', 30),
            'status' => 'published', 'published_at' => now(),
        ]);

        $res = $this->actingAs($admin)->get(route('superadmin.monitoring.jurnal.index', ['bruder' => $bruder1->id]));

        $res->assertOk();
        $res->assertSee('Retret Alpha');
        $res->assertDontSee('Retret Beta');
    }

    public function test_superadmin_can_bulk_delete_bruder_journals(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $bruder = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $j1 = SelfDevelopmentJournal::create([
            'user_id' => $bruder->id, 'activity_date' => '2026-10-01',
            'activity_name' => 'J1', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);
        $j2 = SelfDevelopmentJournal::create([
            'user_id' => $bruder->id, 'activity_date' => '2026-10-02',
            'activity_name' => 'J2', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);
        $j3 = SelfDevelopmentJournal::create([
            'user_id' => $bruder->id, 'activity_date' => '2026-10-03',
            'activity_name' => 'J3', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('superadmin.monitoring.jurnal.destroyBulk'), [
                'ids' => [$j1->id, $j2->id],
            ])
            ->assertRedirect(route('superadmin.monitoring.jurnal.index'));

        $this->assertSoftDeleted('self_development_journals', ['id' => $j1->id]);
        $this->assertSoftDeleted('self_development_journals', ['id' => $j2->id]);
        $this->assertDatabaseHas('self_development_journals', [
            'id' => $j3->id,
            'deleted_at' => null,
        ]);
    }

    public function test_regular_user_cannot_access_superadmin_jurnal_monitoring(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('superadmin.monitoring.jurnal.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('superadmin.monitoring.jurnal.destroyBulk'), ['ids' => [1]])
            ->assertForbidden();
    }
}
