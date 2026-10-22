<?php

namespace Tests\Feature;

use App\Models\DirectMessage;
use App\Models\ProvincialMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenyesuaianTahap1Test extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_account_with_role_and_birth_place(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superadmin)->post(route('superadmin.users.store'), [
            'role' => 'admin',
            'name' => 'Br. Pendamping Baru FIC',
            'username' => 'pendamping_baru',
            'password' => 'secret123',
            'birth_place' => 'Surakarta',
            'birth_date' => '1985-08-17',
            'community_location' => 'Semarang',
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'pendamping_baru',
            'role' => 'admin',
            'birth_place' => 'Surakarta',
        ]);
    }

    public function test_super_admin_can_update_user_role_in_account_management(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($superadmin)->post(route('superadmin.users.account.update', $user), [
            'role' => 'admin',
            'email' => 'new.email@fic.local',
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'admin',
            'email' => 'new.email@fic.local',
        ]);
    }

    public function test_admin_and_super_admin_can_add_bruder_note(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Antonius FIC']);

        $response = $this->actingAs($admin)->post(route('superadmin.users.notes.store', $bruder), [
            'category' => 'Catatan Hidup Pribadi',
            'content' => 'Bruder menunjukkan kematangan pribadi dan semangat persaudaraan yang tinggi.',
        ]);

        $response->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('success', 'Catatan berhasil ditambahkan!');

        $this->assertDatabaseHas('bruder_notes', [
            'user_id' => $bruder->id,
            'author_id' => $admin->id,
            'category' => 'Catatan Hidup Pribadi',
        ]);

        $trackRecord = $this->actingAs($admin)->get(route('superadmin.users.track-record', $bruder));
        $trackRecord->assertOk()
            ->assertSee('Catatan Hidup Pribadi')
            ->assertSee('kematangan pribadi dan semangat persaudaraan');
    }

    public function test_admin_can_send_direct_message_and_bruder_reads_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Br. Dewan TPPBM']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Yohanes FIC']);

        $sendResponse = $this->actingAs($admin)->post(route('superadmin.users.messages.store', $bruder), [
            'title' => 'Pertemuan Evaluasi Khusus',
            'content' => 'Harap hadir di ruang pimpinan pada hari Senin pukul 09:00 WIB.',
        ]);

        $sendResponse->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('direct_messages', [
            'sender_id' => $admin->id,
            'recipient_id' => $bruder->id,
            'title' => 'Pertemuan Evaluasi Khusus',
            'read_at' => null,
        ]);

        // Sisi Bruder: Cek Dashboard & Notifikasi
        $dashboard = $this->actingAs($bruder)->get(route('dashboard'));
        $dashboard->assertOk()
            ->assertSee('Pesan Khusus Baru')
            ->assertSee('Pertemuan Evaluasi Khusus');

        $message = DirectMessage::first();

        // Buka Pesan Khusus
        $readResponse = $this->actingAs($bruder)->get(route('pesan.show', $message));
        $readResponse->assertOk()
            ->assertSee('Pertemuan Evaluasi Khusus')
            ->assertSee('Harap hadir di ruang pimpinan');

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_sender_can_retract_a_direct_message(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['role' => 'admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Penerima Pesan']);

        $own = DirectMessage::create([
            'sender_id' => $superadmin->id,
            'recipient_id' => $bruder->id,
            'title' => 'Pesan Salah Kirim',
            'content' => 'Isi yang harus ditarik sebelum bruder membacanya.',
        ]);
        $fromAdmin = DirectMessage::create([
            'sender_id' => $admin->id,
            'recipient_id' => $bruder->id,
            'title' => 'Arahan TPPBM',
            'content' => 'Pesan dari admin yang hanya super admin atau pengirimnya yang boleh tarik.',
        ]);

        $this->actingAs($bruder)->delete(route('superadmin.users.messages.destroy', [$bruder, $own]))
            ->assertForbidden();

        $this->actingAs($admin)->delete(route('superadmin.users.messages.destroy', [$bruder, $own]))
            ->assertForbidden();

        $this->actingAs($superadmin)->get(route('superadmin.users.messages.create', $bruder))
            ->assertOk()
            ->assertSee('Tarik Pesan')
            ->assertSee('Pesan Salah Kirim');

        $this->actingAs($superadmin)
            ->delete(route('superadmin.users.messages.destroy', [$bruder, $own]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('direct_messages', ['id' => $own->id]);

        $this->actingAs($bruder)->get(route('pesan.index'))
            ->assertOk()
            ->assertDontSee('Pesan Salah Kirim')
            ->assertSee('Arahan TPPBM');

        $this->actingAs($bruder)->get(route('pesan.show', $own))->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('superadmin.users.messages.destroy', [$bruder, $fromAdmin]))
            ->assertRedirect();

        $this->assertDatabaseMissing('direct_messages', ['id' => $fromAdmin->id]);
    }

    public function test_super_admin_can_remove_sapaan_photo_on_edit(): void
    {
        Storage::fake('public');
        $superadmin = User::factory()->create(['role' => 'super_admin']);

        $file = UploadedFile::fake()->image('sapaan.jpg');
        $path = $file->store('sapaan', 'public');

        $sapaan = ProvincialMessage::create([
            'kind' => 'sapaan',
            'title' => 'Sapaan Berfoto',
            'content' => 'Pesan sapaan rohani pembuka bulan dengan foto.',
            'author_name' => 'Br. Provinsial',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia',
            'image' => $path,
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $superadmin->id,
        ]);

        $response = $this->actingAs($superadmin)->put(route('superadmin.sapaan.update', $sapaan), [
            'kind' => 'sapaan',
            'title' => 'Sapaan Berfoto Diperbarui',
            'content' => 'Pesan sapaan rohani yang kini tanpa foto lampiran.',
            'author_name' => 'Br. Provinsial',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia',
            'remove_image' => '1',
        ]);

        $response->assertRedirect(route('superadmin.sapaan.index'));
        $this->assertNull($sapaan->fresh()->image);
    }

    public function test_user_can_update_complete_profile_data(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('profil.update'), [
            'name' => 'Br. Yohanes Pembina FIC',
            'birth_place' => 'Klaten',
            'birth_date' => '1995-12-25',
            'phone' => '081299998888',
            'family_data' => [
                'father_name' => 'Petrus',
                'father_birth_year' => '1960',
                'father_job' => 'Pensiunan Guru',
                'mother_name' => 'Maria',
                'mother_birth_year' => '1965',
                'mother_job' => 'Wiraswasta',
                'older_siblings_count' => '2',
                'younger_siblings_count' => '1',
                'parish_origin' => 'Paroki Maria Assumpta Klaten',
                'emergency_name' => 'Antonius',
                'emergency_relation' => 'Kakak Kandung',
                'emergency_phone' => '081111222333',
            ],
            'education_data' => [
                'sd_name' => 'SD Kanisius Klaten',
                'sd_year' => '2007',
                'smp_name' => 'SMP Pangudi Luhur Klaten',
                'smp_year' => '2010',
                'sma_name' => 'SMA Pangudi Luhur Van Lith',
                'sma_major' => 'IPS',
                'sma_year' => '2013',
            ],
            'formation_data' => [
                'postulat_in' => '2015',
                'postulat_count' => '8',
                'postulat_magister' => 'Br. Stefanus, FIC',
            ],
            'assignment_data' => [
                1 => [
                    'community' => 'Komunitas Ambarawa',
                    'start_year' => '2018',
                    'end_year' => '2021',
                    'duty' => 'Guru Agama',
                ],
            ],
        ]);

        $response->assertRedirect(route('profil'));

        $user = $user->fresh();
        $this->assertSame('Klaten', $user->birth_place);
        $this->assertSame('081299998888', $user->phone);
        $this->assertSame('Petrus', $user->family_data['father_name']);
        $this->assertSame('SD Kanisius Klaten', $user->education_data['sd_name']);
        $this->assertSame('2015', $user->formation_data['postulat_in']);
        $this->assertSame('Komunitas Ambarawa', $user->assignment_data[1]['community']);
    }

    public function test_super_admin_can_update_complete_user_biodata_in_account_management(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Awal FIC']);

        $response = $this->actingAs($superadmin)->post(route('superadmin.users.account.update', $bruder), [
            'update_biodata' => '1',
            'name' => 'Br. Terupdate FIC',
            'email' => 'terupdate@fic.test',
            'birth_place' => 'Yogyakarta',
            'birth_date' => '1992-04-12',
            'phone' => '089988776655',
            'community_location' => 'Komunitas Surakarta',
            'family_data' => [
                'father_name' => 'Yosef',
                'mother_name' => 'Theresia',
            ],
            'education_data' => [
                's1_name' => 'Universitas Sanata Dharma',
                's1_major' => 'Teologi',
            ],
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $fresh = $bruder->fresh();
        $this->assertSame('Br. Terupdate FIC', $fresh->name);
        $this->assertSame('terupdate@fic.test', $fresh->email);
        $this->assertSame('Yogyakarta', $fresh->birth_place);
        $this->assertSame('089988776655', $fresh->phone);
        $this->assertSame('Komunitas Surakarta', $fresh->community_location);
        $this->assertSame('Yosef', $fresh->family_data['father_name']);
        $this->assertSame('Universitas Sanata Dharma', $fresh->education_data['s1_name']);
    }
}
