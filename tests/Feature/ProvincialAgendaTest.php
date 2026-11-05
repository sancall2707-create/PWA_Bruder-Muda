<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincialAgendaTest extends TestCase
{
    use RefreshDatabase;

    private function bruders(int $n = 3)
    {
        return collect(range(1, $n))->map(fn ($i) => User::factory()->create([
            'name' => "Br. Uji {$i} FIC", 'role' => 'user', 'is_active' => true,
        ]));
    }

    public function test_siaran_agenda_ditandai_provinsi_dan_tampil_sekali_di_beranda_super_admin(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->bruders(3);

        $this->actingAs($sa)->post(route('superadmin.monitoring.agenda.store'), [
            'title' => 'Kapitel Provinsi', 'start_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00', 'location' => 'Temanggung',
        ])->assertRedirect();

        $this->assertSame(3, PersonalAgenda::where('is_provincial', true)->count());
        $this->assertSame(1, PersonalAgenda::where('is_provincial', true)->distinct('broadcast_key')->count('broadcast_key'));

        $html = $this->actingAs($sa)->get(route('superadmin.dashboard'))->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'margin-top: 2px;">Kapitel Provinsi</div>'));
        $this->assertStringContainsString('Agenda Provinsi', $html);
        $this->assertStringContainsString('>TPPBM<', $html);
        // Agenda provinsi tidak boleh dirinci per bruder pada daftar agenda terdekat.
        $this->assertSame(0, substr_count($html, 'Br. Uji 1 FIC</span>\n                        <span style="font-size: 11px; font-weight: 700; color: #0E3366;">Agenda Provinsi'));
    }

    public function test_beranda_admin_menampilkan_agenda_provinsi_satu_kali(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->bruders(4);

        $this->actingAs($admin)->post(route('superadmin.monitoring.agenda.store'), [
            'title' => 'Retret Provinsi', 'start_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '08:00', 'location' => 'Muntilan',
        ])->assertRedirect();

        $html = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'margin-top: 2px;">Retret Provinsi</div>'));
        $this->assertStringContainsString('Agenda Provinsi', $html);
    }

    public function test_agenda_pribadi_bruder_tetap_agenda_pribadi_dan_tampil_per_bruder(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        [$b1] = $this->bruders(2);

        PersonalAgenda::create([
            'user_id' => $b1->id, 'title' => 'Dolan', 'start_at' => now()->addDay(),
            'location' => 'Parakan', 'status' => 'scheduled',
        ]);

        $html = $this->actingAs($sa)->get(route('superadmin.dashboard'))->getContent();
        $this->assertStringContainsString('Agenda Pribadi', $html);
        $this->assertStringContainsString('Br. Uji 1 FIC', $html);
        $this->assertFalse((bool) PersonalAgenda::where('title', 'Dolan')->first()->is_provincial);
    }

    public function test_agenda_lainnya_ditulis_admin_otomatis_jadi_agenda_provinsi(): void
    {
        foreach (['admin', 'super_admin'] as $role) {
            $penulis = User::factory()->create(['role' => $role, 'is_active' => true]);

            $this->actingAs($penulis)->post(route('agenda.store'), [
                'agenda_type' => 'lainnya', 'title' => "Rapat {$role}",
                'start_date' => now()->addDays(4)->format('Y-m-d'), 'start_time' => '10:00',
                'location' => 'Semarang',
            ])->assertRedirect();

            $a = PersonalAgenda::where('title', "Rapat {$role}")->firstOrFail();
            $this->assertTrue($a->is_provincial, "{$role} harus menghasilkan Agenda Provinsi");
        }
    }

    public function test_agenda_lainnya_ditulis_bruder_tetap_pribadi(): void
    {
        [$b] = $this->bruders(1);

        $this->actingAs($b)->post(route('agenda.store'), [
            'agenda_type' => 'lainnya', 'title' => 'Kegiatan Komunitas',
            'start_date' => now()->addDays(4)->format('Y-m-d'), 'start_time' => '10:00',
            'location' => 'Komunitas',
        ])->assertRedirect();

        $a = PersonalAgenda::where('title', 'Kegiatan Komunitas')->firstOrFail();
        $this->assertFalse($a->is_provincial);
        $this->assertNull($a->broadcast_key);
    }
}
