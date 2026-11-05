<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincialAgendaEditDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(string $role = 'super_admin'): array
    {
        $penulis = User::factory()->create(['role' => $role, 'is_active' => true]);
        foreach (range(1, 3) as $i) {
            User::factory()->create(['name' => "Br. Uji {$i} FIC", 'role' => 'user', 'is_active' => true]);
        }

        $this->actingAs($penulis)->post(route('superadmin.monitoring.agenda.store'), [
            'title' => 'Kapitel', 'start_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00', 'location' => 'Temanggung', 'description' => 'Awal',
        ])->assertRedirect();

        return [$penulis, PersonalAgenda::where('title', 'Kapitel')->firstOrFail()->broadcast_key];
    }

    public function test_daftar_menampilkan_agenda_provinsi_satu_kali_dengan_tombol_edit_hapus(): void
    {
        [$sa] = $this->siapkan();

        $html = $this->actingAs($sa)->get(route('superadmin.monitoring.agenda', [
            'bulan' => now()->addDays(5)->month, 'tahun' => now()->addDays(5)->year,
        ]))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Diterima 3 akun bruder'));
        $this->assertStringContainsString('TPPBM', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Hapus', $html);
    }

    public function test_edit_memperbarui_semua_salinan_sekaligus(): void
    {
        foreach (['super_admin', 'admin'] as $role) {
            PersonalAgenda::query()->forceDelete();
            User::query()->delete();
            [$penulis, $key] = $this->siapkan($role);

            $this->actingAs($penulis)->get(route('superadmin.monitoring.agenda.edit', $key))
                ->assertOk()->assertSee('Edit Agenda Provinsi')->assertSee('value="Kapitel"', false)->assertSee('<strong>3 akun Bruder Muda</strong>', false);

            $tgl = now()->addDays(9)->format('Y-m-d');
            $this->actingAs($penulis)->put(route('superadmin.monitoring.agenda.update', $key), [
                'title' => 'Kapitel Revisi', 'start_date' => $tgl, 'start_time' => '13:30',
                'location' => 'Semarang', 'description' => 'Diubah',
            ])->assertRedirect()->assertSessionHas('success');

            $this->assertSame(3, PersonalAgenda::where('title', 'Kapitel Revisi')->count());
            $this->assertSame(0, PersonalAgenda::where('title', 'Kapitel')->count());
            $this->assertSame(3, PersonalAgenda::where('location', 'Semarang')->where('is_provincial', true)->count());
            $this->assertSame(1, PersonalAgenda::where('is_provincial', true)->distinct()->count('broadcast_key'));
            $this->assertSame("{$tgl} 13:30:00", PersonalAgenda::first()->start_at->format('Y-m-d H:i:s'));
        }
    }

    public function test_edit_tidak_menyentuh_agenda_pribadi_bruder(): void
    {
        [$sa, $key] = $this->siapkan();
        $bruder = User::where('role', 'user')->first();
        $pribadi = PersonalAgenda::create([
            'user_id' => $bruder->id, 'title' => 'Dolan', 'start_at' => now()->addDay(),
            'location' => 'Parakan', 'status' => 'scheduled',
        ]);

        $this->actingAs($sa)->put(route('superadmin.monitoring.agenda.update', $key), [
            'title' => 'Baru', 'start_date' => now()->addDays(7)->format('Y-m-d'),
            'start_time' => '08:00', 'location' => 'X',
        ])->assertRedirect();
        $this->actingAs($sa)->delete(route('superadmin.monitoring.agenda.destroy', md5('Baru|'.now()->addDays(7)->format('Y-m-d').' 08:00:00|X')))
            ->assertRedirect();

        $pribadi->refresh();
        $this->assertSame('Dolan', $pribadi->title);
        $this->assertNull($pribadi->deleted_at);
    }

    public function test_hapus_menghapus_semua_salinan_dengan_soft_delete(): void
    {
        [$sa, $key] = $this->siapkan();

        $this->actingAs($sa)->delete(route('superadmin.monitoring.agenda.destroy', $key))
            ->assertRedirect(route('superadmin.monitoring.agenda'))->assertSessionHas('success');

        $this->assertSame(0, PersonalAgenda::where('title', 'Kapitel')->count());
        $this->assertSame(3, PersonalAgenda::withTrashed()->where('title', 'Kapitel')->count());
        $this->assertSoftDeleted('personal_agendas', ['title' => 'Kapitel']);
    }

    public function test_validasi_edit_dan_kunci_tidak_dikenal(): void
    {
        [$sa, $key] = $this->siapkan();

        $this->actingAs($sa)->put(route('superadmin.monitoring.agenda.update', $key), ['title' => ''])
            ->assertSessionHasErrors(['title', 'start_date', 'start_time', 'location']);
        $this->assertSame(3, PersonalAgenda::where('title', 'Kapitel')->count());

        $asing = str_repeat('a', 32);
        $this->actingAs($sa)->get(route('superadmin.monitoring.agenda.edit', $asing))->assertRedirect();
        $this->actingAs($sa)->delete(route('superadmin.monitoring.agenda.destroy', $asing))->assertSessionHas('error');
    }

    public function test_bruder_biasa_tidak_boleh_edit_atau_hapus_agenda_provinsi(): void
    {
        [, $key] = $this->siapkan();
        $bruder = User::where('role', 'user')->first();

        $this->actingAs($bruder)->get(route('superadmin.monitoring.agenda.edit', $key))->assertForbidden();
        $this->actingAs($bruder)->put(route('superadmin.monitoring.agenda.update', $key), [
            'title' => 'X', 'start_date' => '2026-12-01', 'start_time' => '09:00', 'location' => 'X',
        ])->assertForbidden();
        $this->actingAs($bruder)->delete(route('superadmin.monitoring.agenda.destroy', $key))->assertForbidden();

        $this->assertSame(3, PersonalAgenda::where('title', 'Kapitel')->count());
    }
}
