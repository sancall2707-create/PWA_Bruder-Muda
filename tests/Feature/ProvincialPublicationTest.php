<?php

namespace Tests\Feature;

use App\Models\ProvincialMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProvincialPublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_sapaan_and_photo_appear_for_the_bruder(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);

        $this->actingAs($admin)->post(route('superadmin.sapaan.store'), [
            'kind' => 'sapaan',
            'title' => 'Sapaan Uji Kesetiaan',
            'author_name' => 'Br. Agustinus Giwal Santoso, FIC',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia',
            'content' => "Paragraf pertama sapaan uji.\nParagraf kedua tetap utuh.",
            'image' => UploadedFile::fake()->createWithContent('sapaan.jpg', base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=')),
        ])->assertRedirect(route('superadmin.sapaan.index'));

        $message = ProvincialMessage::query()->first();
        $this->assertNotNull($message);
        $this->assertTrue($message->is_active);
        $this->assertSame('sapaan', $message->kind);
        Storage::disk('public')->assertExists($message->image);

        $this->actingAs($bruder)
            ->get(route('sapaan-provinsial'))
            ->assertOk()
            ->assertSee('Sapaan Uji Kesetiaan')
            ->assertSee('Paragraf pertama sapaan uji.')
            ->assertSee('Paragraf kedua tetap utuh.')
            ->assertSee('Pemimpin Provinsial FIC Indonesia')
            ->assertSee(route('sapaan-provinsial.foto', $message), false)
            ->assertDontSee('Menghidupi Panggilan dengan Sukacita dan Kesetiaan');

        $this->actingAs($bruder)
            ->get(route('sapaan-provinsial.foto', $message))
            ->assertOk();

        $this->actingAs($bruder)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sapaan Uji Kesetiaan')
            ->assertSee('Sapaan Provinsial');
    }

    public function test_new_sapaan_replaces_the_previous_one_without_removing_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user']);

        $this->publish($admin, [
            'kind' => 'sapaan',
            'title' => 'Sapaan Lama Uji',
            'content' => 'Isi sapaan lama yang sudah diganti.',
        ]);
        $this->publish($admin, [
            'kind' => 'pengumuman',
            'title' => 'Rekoleksi Bersama Uji',
            'content' => 'Seluruh bruder muda hadir di aula komunitas.',
            'tanggal_kegiatan' => '2026-10-15',
            'waktu_kegiatan' => '09:30',
        ]);
        $this->publish($admin, [
            'kind' => 'sapaan',
            'title' => 'Sapaan Baru Uji',
            'content' => 'Isi sapaan baru yang sedang tayang.',
        ]);

        $old = ProvincialMessage::query()->where('title', 'Sapaan Lama Uji')->first();
        $fresh = ProvincialMessage::query()->where('title', 'Sapaan Baru Uji')->first();
        $announcement = ProvincialMessage::query()->where('title', 'Rekoleksi Bersama Uji')->first();

        $this->assertFalse($old->is_active);
        $this->assertTrue($fresh->is_active);
        $this->assertTrue($announcement->is_active);

        $this->actingAs($bruder)
            ->get(route('sapaan-provinsial'))
            ->assertOk()
            ->assertSee('Sapaan Baru Uji')
            ->assertSee('Rekoleksi Bersama Uji')
            ->assertSee('15 Oktober 2026')
            ->assertSee('09:30 WIB')
            ->assertDontSee('Sapaan Lama Uji');

        $this->actingAs($bruder)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rekoleksi Bersama Uji')
            ->assertSee('Pengumuman')
            ->assertSee('15 Oktober 2026')
            ->assertDontSee('Sapaan Lama Uji');
    }

    public function test_withdrawn_announcement_leaves_the_reminder(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user']);

        $this->publish($admin, [
            'kind' => 'pengumuman',
            'title' => 'Pengumuman Ditarik Uji',
            'content' => 'Pengumuman ini tidak lagi berlaku.',
        ]);

        $message = ProvincialMessage::query()->where('title', 'Pengumuman Ditarik Uji')->first();

        $this->actingAs($admin)
            ->post(route('superadmin.sapaan.status', $message))
            ->assertRedirect(route('superadmin.sapaan.index'));

        $this->assertFalse($message->fresh()->is_active);

        $this->actingAs($bruder)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Pengumuman Ditarik Uji');
    }

    public function test_super_admin_can_edit_a_publication(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->publish($admin, [
            'kind' => 'sapaan',
            'title' => 'Judul Sebelum Diubah',
            'content' => 'Isi sapaan sebelum perubahan.',
        ]);
        $message = ProvincialMessage::query()->first();

        $this->actingAs($admin)->put(route('superadmin.sapaan.update', $message), [
            'kind' => 'sapaan',
            'title' => 'Judul Sesudah Diubah',
            'author_name' => 'TPPBM Provinsi',
            'author_role' => 'TPPBM',
            'content' => 'Isi sapaan sesudah perubahan.',
        ])->assertRedirect(route('superadmin.sapaan.index'));

        $this->assertSame('Judul Sesudah Diubah', $message->fresh()->title);
        $this->assertSame('TPPBM', $message->fresh()->author_role);
    }

    public function test_bruder_cannot_publish_and_short_text_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->post(route('superadmin.sapaan.store'), [
            'kind' => 'sapaan',
            'title' => 'Sapaan Gelap',
            'author_name' => 'Anonim',
            'author_role' => 'TPPBM',
            'content' => 'Pesan yang tidak boleh tersimpan.',
        ])->assertForbidden();

        $this->actingAs($admin)->post(route('superadmin.sapaan.store'), [
            'kind' => 'sapaan',
            'title' => 'Terlalu Pendek',
            'author_name' => 'Provinsial',
            'author_role' => 'TPPBM',
            'content' => 'pendek',
        ])->assertSessionHasErrors('content');

        $this->assertSame(0, ProvincialMessage::query()->count());
    }

    public function test_super_admin_and_admin_can_delete_a_publication(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create(['role' => 'user']);

        $this->publish($admin, [
            'kind' => 'pengumuman',
            'title' => 'Pengumuman Dihapus Uji',
            'content' => 'Pengumuman yang akan dihapus dari sistem.',
        ]);

        $message = ProvincialMessage::query()->where('title', 'Pengumuman Dihapus Uji')->first();
        $this->assertNotNull($message);

        // Bruder tidak bisa menghapus
        $this->actingAs($bruder)->delete(route('superadmin.sapaan.destroy', $message))
            ->assertForbidden();

        // Admin bisa menghapus
        $this->actingAs($admin)->delete(route('superadmin.sapaan.destroy', $message))
            ->assertRedirect(route('superadmin.sapaan.index'))
            ->assertSessionHas('success', 'Pengumuman berhasil dihapus.');

        $this->assertSoftDeleted('provincial_messages', ['id' => $message->id]);
    }

    public function test_sapaan_label_adapts_to_tppbm_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create(['role' => 'user']);

        $this->publish($admin, [
            'kind' => 'sapaan',
            'title' => 'Sapaan dari Tim Pendamping',
            'author_name' => 'Tim Pendamping TPPBM',
            'author_role' => 'TPPBM',
            'content' => 'Isi pesan pembinaan dari TPPBM untuk bruder muda.',
        ]);

        $this->actingAs($bruder)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sapaan TPPBM')
            ->assertDontSee('Sapaan Provinsial');

        $this->actingAs($bruder)
            ->get(route('sapaan-provinsial'))
            ->assertOk()
            ->assertSee('Sapaan TPPBM');
    }

    /**
     * @param  array<string, string>  $overrides
     */
    private function publish(User $admin, array $overrides): void
    {
        $this->actingAs($admin)->post(route('superadmin.sapaan.store'), array_merge([
            'kind' => 'sapaan',
            'title' => 'Sapaan Uji',
            'author_name' => 'Br. Agustinus Giwal Santoso, FIC',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia',
            'content' => 'Isi sapaan yang cukup panjang untuk dipublikasikan.',
        ], $overrides))->assertRedirect(route('superadmin.sapaan.index'));
    }
}
