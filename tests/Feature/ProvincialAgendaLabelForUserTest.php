<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincialAgendaLabelForUserTest extends TestCase
{
    use RefreshDatabase;

    private function buat(User $u, string $judul, bool $provinsi): PersonalAgenda
    {
        return PersonalAgenda::create([
            'user_id' => $u->id, 'title' => $judul, 'start_at' => now()->addDays(2)->setTime(9, 0), 'location' => 'Lokasi Uji',
            'status' => 'scheduled', 'is_provincial' => $provinsi, 'broadcast_key' => $provinsi ? md5($judul) : null,
        ]);
    }

    private function kartu(string $html, string $judul): string
    {
        $pos = strpos($html, $judul);
        $this->assertNotFalse($pos, "judul {$judul} tidak ditemukan");

        return substr($html, $pos, 700);
    }

    public function test_daftar_agenda_menulis_agenda_provinsi_bukan_agenda_pribadi(): void
    {
        $b = User::factory()->create(['role' => 'user']);
        $this->buat($b, 'Kapitel Provinsi Uji', true);
        $this->buat($b, 'Dolan Pribadi Uji', false);

        $html = $this->actingAs($b)->get(route('agenda.index', ['bulan' => now()->addDays(2)->format('Y-m')]))->assertOk()->getContent();

        $this->assertStringContainsString('Agenda Provinsi', $this->kartu($html, 'Kapitel Provinsi Uji'));
        $this->assertStringNotContainsString('Agenda pribadi', $this->kartu($html, 'Kapitel Provinsi Uji'));
        $this->assertStringContainsString('Agenda pribadi', $this->kartu($html, 'Dolan Pribadi Uji'));
    }

    public function test_kalender_menulis_agenda_provinsi_bukan_agenda_pribadi(): void
    {
        $b = User::factory()->create(['role' => 'user']);
        $prov = $this->buat($b, 'Kapitel Kalender Uji', true);
        $tgl = $prov->start_at->toDateString();

        $html = $this->actingAs($b)->get(route('kalender', ['date' => $tgl, 'tanggal' => $tgl, 'bulan' => $prov->start_at->format('Y-m')]))->assertOk()->getContent();

        $this->assertStringContainsString('Agenda Provinsi', $this->kartu($html, 'Kapitel Kalender Uji'));
        $this->assertStringNotContainsString('Agenda pribadi', $this->kartu($html, 'Kapitel Kalender Uji'));
    }

    public function test_pengingat_di_beranda_menulis_agenda_provinsi(): void
    {
        $b = User::factory()->create(['role' => 'user']);
        $this->buat($b, 'Kapitel Pengingat Uji', true);

        $html = $this->actingAs($b)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Agenda Provinsi', $this->kartu($html, 'Kapitel Pengingat Uji'));
        $this->assertStringNotContainsString('Agenda pribadi', $this->kartu($html, 'Kapitel Pengingat Uji'));
    }

    public function test_agenda_pribadi_tetap_bertuliskan_agenda_pribadi_di_beranda(): void
    {
        $b = User::factory()->create(['role' => 'user']);
        $this->buat($b, 'Dolan Beranda Uji', false);

        $html = $this->actingAs($b)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Agenda pribadi', $this->kartu($html, 'Dolan Beranda Uji'));
    }
}
