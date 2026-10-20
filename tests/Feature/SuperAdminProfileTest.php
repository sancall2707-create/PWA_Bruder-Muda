<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_profile_page(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Super Admin FIC',
            'username' => 'superadmin',
        ]);

        $response = $this->actingAs($superadmin)->get(route('superadmin.profil'));

        $response->assertOk()
            ->assertSee('Profil & Edit Akun')
            ->assertSee('Super Admin FIC')
            ->assertSee('superadmin')
            ->assertSee('SIMPAN PERUBAHAN PROFIL')
            ->assertSee('PERBARUI KATA SANDI');
    }

    public function test_super_admin_can_update_profile_and_upload_photo(): void
    {
        Storage::fake('public');
        $superadmin = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Super Admin Awal',
            'email' => 'superadmin.awal@fic.local',
        ]);

        $photo = UploadedFile::fake()->image('admin_avatar.jpg');

        $response = $this->actingAs($superadmin)->post(route('superadmin.profil.update'), [
            'name' => 'Super Admin Pembaruan',
            'email' => 'superadmin.baru@fic.local',
            'phone' => '081233344455',
            'community_location' => 'Kantor Pusat Provinsi Semarang',
            'bio' => 'Pengelola sistem formasi dan pembinaan FIC Indonesia.',
            'profile_photo' => $photo,
        ]);

        $response->assertRedirect(route('superadmin.profil'))
            ->assertSessionHas('success');

        $fresh = $superadmin->fresh();
        $this->assertSame('Super Admin Pembaruan', $fresh->name);
        $this->assertSame('superadmin.baru@fic.local', $fresh->email);
        $this->assertSame('081233344455', $fresh->phone);
        $this->assertSame('Kantor Pusat Provinsi Semarang', $fresh->community_location);
        $this->assertNotNull($fresh->profile_photo);
        Storage::disk('public')->assertExists($fresh->profile_photo);
    }

    public function test_super_admin_can_update_own_password(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('SuperAdmin#FIC2026'),
        ]);

        $response = $this->actingAs($superadmin)->post(route('superadmin.profil.password'), [
            'current_password' => 'SuperAdmin#FIC2026',
            'password' => 'SuperAdminBaru#2027',
            'password_confirmation' => 'SuperAdminBaru#2027',
        ]);

        $response->assertRedirect(route('superadmin.profil'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('SuperAdminBaru#2027', $superadmin->fresh()->password));
    }

    public function test_bruder_cannot_access_super_admin_profile_page(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('superadmin.profil'))->assertForbidden();
        $this->actingAs($bruder)->post(route('superadmin.profil.update'), [
            'name' => 'Begal Admin',
        ])->assertForbidden();
    }
}
