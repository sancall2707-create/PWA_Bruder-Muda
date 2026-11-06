<?php

namespace Tests\Feature;

use App\Models\ProvincialMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinsiRoleLabelTest extends TestCase
{
    use RefreshDatabase;

    private function sapaanLama(string $role): ProvincialMessage
    {
        return ProvincialMessage::create([
            'kind' => 'sapaan', 'title' => 'Sapaan Data Lama', 'content' => 'Isi sapaan data lama yang cukup panjang.',
            'author_name' => 'Br. Contoh, FIC', 'author_role' => $role,
            'is_active' => true, 'published_at' => now(), 'created_by' => User::factory()->create(['role' => 'super_admin'])->id,
        ]);
    }

    public function test_data_lama_bertuliskan_provinsial_tampil_sebagai_provinsi_tanpa_mengubah_database(): void
    {
        $pesan = $this->sapaanLama('Pemimpin Provinsial FIC Indonesia');
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('sapaan-provinsial'))
            ->assertOk()
            ->assertSee('Pemimpin Provinsi FIC Indonesia')
            ->assertDontSee('Pemimpin Provinsial');

        $this->assertSame('Pemimpin Provinsial FIC Indonesia', $pesan->fresh()->author_role, 'database tidak boleh diubah oleh tampilan');
    }

    public function test_data_baru_disimpan_dengan_penulisan_provinsi(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->post(route('superadmin.sapaan.store'), [
            'kind' => 'sapaan', 'title' => 'Sapaan Baru', 'author_name' => 'Br. Contoh, FIC',
            'author_role' => 'Pemimpin Provinsi FIC Indonesia', 'content' => 'Isi sapaan baru yang cukup panjang.',
        ])->assertRedirect(route('superadmin.sapaan.index'));

        $this->assertSame('Pemimpin Provinsi FIC Indonesia', ProvincialMessage::query()->latest('id')->first()->author_role);
    }

    public function test_menyimpan_dengan_penulisan_lama_tetap_diterima_dan_dinormalkan(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->post(route('superadmin.sapaan.store'), [
            'kind' => 'sapaan', 'title' => 'Sapaan Cache Lama', 'author_name' => 'Br. Contoh, FIC',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia', 'content' => 'Isi sapaan dari formulir lama yang cukup panjang.',
        ])->assertRedirect(route('superadmin.sapaan.index'));

        $this->assertSame('Pemimpin Provinsi FIC Indonesia', ProvincialMessage::query()->latest('id')->first()->author_role);
    }

    public function test_form_admin_menampilkan_pilihan_provinsi_dan_memilihnya_untuk_data_lama(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $pesan = $this->sapaanLama('Pemimpin Provinsial FIC Indonesia');

        $html = $this->actingAs($admin)->get(route('superadmin.sapaan.edit', $pesan))->assertOk()->getContent();

        $this->assertStringContainsString('value="Pemimpin Provinsi FIC Indonesia" selected', preg_replace('/\s+/', ' ', $html));
        $this->assertStringNotContainsString('Pemimpin Provinsial', $html);
    }

    public function test_peran_tppbm_tidak_berubah(): void
    {
        $this->sapaanLama('TPPBM');
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('sapaan-provinsial'))->assertOk()->assertSee('TPPBM');
    }
}
