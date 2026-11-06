<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincialAgendaLockedForUserTest extends TestCase
{
    use RefreshDatabase;

    private function agenda(User $pemilik, bool $provinsi, string $judul): PersonalAgenda
    {
        return PersonalAgenda::create([
            'user_id' => $pemilik->id, 'title' => $judul, 'start_at' => now()->addDays(2)->setTime(9, 0),
            'location' => 'Lokasi Uji', 'status' => 'scheduled',
            'is_provincial' => $provinsi, 'broadcast_key' => $provinsi ? md5($judul) : null,
        ]);
    }

    public function test_bruder_tidak_bisa_membuka_form_edit_agenda_provinsi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $a = $this->agenda($bruder, true, 'Kapitel Uji');

        $this->actingAs($bruder)->get(route('agenda.edit', ['type' => 'lainnya', 'schedule' => $a->id]))->assertForbidden();
    }

    public function test_bruder_tidak_bisa_mengubah_agenda_provinsi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $a = $this->agenda($bruder, true, 'Kapitel Uji');

        $this->actingAs($bruder)->put(route('agenda.update', ['type' => 'lainnya', 'schedule' => $a->id]), [
            'title' => 'Diubah Paksa', 'start_date' => now()->addDays(3)->toDateString(), 'start_time' => '10:00', 'location' => 'Tempat Lain',
        ])->assertForbidden();

        $this->assertSame('Kapitel Uji', $a->fresh()->title);
        $this->assertSame('Lokasi Uji', $a->fresh()->location);
    }

    public function test_bruder_tidak_bisa_menghapus_agenda_provinsi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $a = $this->agenda($bruder, true, 'Kapitel Uji');

        $this->actingAs($bruder)->delete(route('agenda.destroy', ['type' => 'lainnya', 'schedule' => $a->id]))->assertForbidden();

        $this->assertNotNull(PersonalAgenda::find($a->id));
        $this->assertNull($a->fresh()->deleted_at);
    }

    public function test_agenda_pribadi_bruder_tetap_bisa_diedit_dan_dihapus(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $a = $this->agenda($bruder, false, 'Dolan Uji');

        $this->actingAs($bruder)->get(route('agenda.edit', ['type' => 'lainnya', 'schedule' => $a->id]))->assertOk();
        $this->actingAs($bruder)->put(route('agenda.update', ['type' => 'lainnya', 'schedule' => $a->id]), [
            'title' => 'Dolan Diperbarui', 'start_date' => now()->addDays(3)->toDateString(), 'start_time' => '10:00', 'location' => 'Parakan',
        ])->assertRedirect(route('agenda.index'));
        $this->assertSame('Dolan Diperbarui', $a->fresh()->title);

        $this->actingAs($bruder)->delete(route('agenda.destroy', ['type' => 'lainnya', 'schedule' => $a->id]))->assertRedirect(route('agenda.index'));
        $this->assertSoftDeleted('personal_agendas', ['id' => $a->id]);
    }

    public function test_tombol_edit_dan_hapus_disembunyikan_hanya_untuk_agenda_provinsi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $prov = $this->agenda($bruder, true, 'Kapitel Terkunci');
        $pribadi = $this->agenda($bruder, false, 'Dolan Terbuka');

        $html = $this->actingAs($bruder)->get(route('agenda.index', ['bulan' => now()->addDays(2)->format('Y-m')]))->assertOk()->getContent();
        $this->assertStringContainsString('Kapitel Terkunci', $html);
        $this->assertStringContainsString('Dolan Terbuka', $html);
        $this->assertStringNotContainsString("schedule' => {$prov->id}", $html);
        $this->assertStringContainsString('/agenda/lainnya/'.$pribadi->id.'/edit', $html);
        $this->assertStringNotContainsString('/agenda/lainnya/'.$prov->id.'/edit', $html);
        $this->assertStringNotContainsString('/agenda/lainnya/'.$prov->id.'"', $html);
    }

    public function test_kalender_menyembunyikan_edit_hapus_agenda_provinsi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $prov = $this->agenda($bruder, true, 'Kapitel Terkunci');
        $tgl = $prov->start_at->toDateString();

        $html = $this->actingAs($bruder)->get(route('kalender', ['tanggal' => $tgl, 'date' => $tgl, 'bulan' => $prov->start_at->format('Y-m')]))->assertOk()->getContent();
        $this->assertStringNotContainsString('/agenda/lainnya/'.$prov->id.'/edit', $html);
        $this->assertStringNotContainsString('/agenda/lainnya/'.$prov->id.'"', $html);
    }

    public function test_agenda_provinsi_tetap_tampil_untuk_bruder(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        $this->agenda($bruder, true, 'Kapitel Terlihat');

        $this->actingAs($bruder)->get(route('agenda.index', ['bulan' => now()->addDays(2)->format('Y-m')]))->assertOk()->assertSee('Kapitel Terlihat');
    }

    public function test_admin_tetap_bisa_mengelola_agenda_provinsi_lewat_monitoring(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user']);
        $a = $this->agenda($bruder, true, 'Kapitel Admin');

        $this->actingAs($admin)->get(route('superadmin.monitoring.agenda', ['bulan' => now()->addDays(2)->format('Y-m')]))->assertOk()->assertSee('Kapitel Admin');
    }

    public function test_bruder_tidak_bisa_menyentuh_agenda_milik_bruder_lain(): void
    {
        $a = User::factory()->create(['role' => 'user']);
        $b = User::factory()->create(['role' => 'user']);
        $punyaB = $this->agenda($b, false, 'Milik B');

        $this->actingAs($a)->delete(route('agenda.destroy', ['type' => 'lainnya', 'schedule' => $punyaB->id]))->assertNotFound();
        $this->assertNull($punyaB->fresh()->deleted_at);
    }
}
