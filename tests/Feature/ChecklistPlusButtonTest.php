<?php

namespace Tests\Feature;

use App\Models\SpiritualGuidanceReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistPlusButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_belum_mengisi_tampil_tombol_plus_tanpa_teks_isi(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $html = $this->actingAs($bruder)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringNotContainsString('+ Isi', $html);
        foreach (['Isi Bimbingan Rohani', 'Isi Wawanhati Pimpinan Lokal', 'Isi Jurnal Pengembangan Diri'] as $label) {
            $this->assertStringContainsString('aria-label="'.$label.'"', $html);
        }
        $this->assertStringContainsString(route('bimbingan.create'), $html);
        $this->assertStringContainsString(route('wawanhati.create'), $html);
        $this->assertStringContainsString(route('jurnal.create'), $html);
        $this->assertSame(3, substr_count($html, 'M12 5v14M5 12h14'));
    }

    public function test_yang_sudah_terisi_tetap_tombol_lihat(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);
        SpiritualGuidanceReport::create([
            'user_id' => $bruder->id, 'meeting_at' => now(), 'mentor_name' => 'Br. Contoh',
            'location' => 'Ruang Contoh', 'report_content' => str_repeat('a', 220), 'status' => 'submitted',
        ]);

        $html = $this->actingAs($bruder)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Lihat &rarr;', $html);
        $this->assertStringNotContainsString('aria-label="Isi Bimbingan Rohani"', $html);
        $this->assertStringContainsString('aria-label="Isi Wawanhati Pimpinan Lokal"', $html);
    }
}
