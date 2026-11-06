<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLandingOnSapaanTest extends TestCase
{
    use RefreshDatabase;

    private function akun(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    public function test_bruder_yang_membuka_aplikasi_diarahkan_ke_sapaan_dan_pengumuman(): void
    {
        $this->actingAs($this->akun('user'))
            ->get('/')
            ->assertRedirect('/sapaan-provinsial');
    }

    public function test_halaman_sapaan_bisa_dibuka_dan_punya_tombol_ke_beranda(): void
    {
        $this->actingAs($this->akun('user'))
            ->get('/sapaan-provinsial')
            ->assertOk()
            ->assertSee('MASUK KE BERANDA');
    }

    public function test_beranda_bruder_tetap_bisa_dibuka_langsung(): void
    {
        $this->actingAs($this->akun('user'))->get('/dashboard')->assertOk();
    }

    public function test_login_bruder_tetap_ke_sapaan(): void
    {
        $user = $this->akun('user');
        $user->password = 'rahasia123';
        $user->save();

        $this->post('/login', ['username' => $user->username, 'password' => 'rahasia123'])
            ->assertRedirect('/sapaan-provinsial');
    }

    public function test_peran_lain_tidak_berubah(): void
    {
        $this->actingAs($this->akun('super_admin'))->get('/')->assertRedirect('/super-admin');
        $this->actingAs($this->akun('admin'))->get('/')->assertRedirect('/admin');
    }

    public function test_tamu_tetap_diarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_bruder_tanpa_sapaan_tetap_melihat_halaman_dengan_tombol_masuk(): void
    {
        $this->actingAs($this->akun('user'))
            ->get('/sapaan-provinsial')
            ->assertOk()
            ->assertSee('Belum ada sapaan')
            ->assertSee('MASUK KE BERANDA');
    }
}
