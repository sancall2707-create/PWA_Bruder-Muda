<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BruderMudaRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_bimbingan_report_and_logbook_are_stored_for_the_owner(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $other = User::factory()->create();
        $content = $this->narrative();

        $response = $this->actingAs($user)->post(route('bimbingan.store'), [
            'meeting_date' => now()->toDateString(),
            'mentor_name' => 'Br. Markus Uji, FIC',
            'location' => 'Ruang Bimbingan Ambarawa',
            'report_content' => $content,
            'proof_file' => UploadedFile::fake()->createWithContent('logbook.jpg', $this->tinyJpeg()),
        ]);

        $response->assertRedirect(route('bimbingan.index'));
        $report = SpiritualGuidanceReport::first();
        $this->assertNotNull($report);
        $this->assertSame($user->id, $report->user_id);
        $this->assertSame('submitted', $report->status);
        $this->assertSame($content, $report->report_content);
        Storage::disk('local')->assertExists($report->proof_file);

        $this->actingAs($user)
            ->get(route('bimbingan.index'))
            ->assertOk()
            ->assertSee('Br. Markus Uji, FIC')
            ->assertSee('Unduh logbook');

        $this->actingAs($user)
            ->get(route('bimbingan.proof', $report))
            ->assertOk();

        $this->actingAs($other)
            ->get(route('bimbingan.index'))
            ->assertOk()
            ->assertDontSee('Br. Markus Uji, FIC');

        $this->actingAs($other)
            ->get(route('bimbingan.proof', $report))
            ->assertNotFound();
    }

    public function test_report_content_must_be_between_200_and_500_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('bimbingan.store'), [
            'meeting_date' => now()->toDateString(),
            'mentor_name' => 'Br. Markus Uji, FIC',
            'location' => 'Ruang Bimbingan',
            'report_content' => 'Terlalu singkat.',
        ])->assertSessionHasErrors('report_content');

        $this->assertSame(0, SpiritualGuidanceReport::count());
    }

    public function test_wawanhati_report_is_stored(): void
    {
        $user = User::factory()->create();
        $content = $this->narrative();

        $this->actingAs($user)->post(route('wawanhati.store'), [
            'meeting_date' => now()->toDateString(),
            'local_leader_name' => 'Br. Frans Uji, FIC',
            'location' => 'Ruang Piko',
            'report_content' => $content,
        ])->assertRedirect(route('wawanhati.index'));

        $this->assertDatabaseHas('wawanhati_reports', [
            'user_id' => $user->id,
            'local_leader_name' => 'Br. Frans Uji, FIC',
            'status' => 'submitted',
        ]);
        $this->assertInstanceOf(WawanhatiReport::class, WawanhatiReport::first());
    }

    public function test_three_agenda_types_are_stored_in_chronological_order(): void
    {
        $user = User::factory()->create();
        $later = now()->addDays(3)->toDateString();
        $sooner = now()->addDay()->toDateString();

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'lainnya',
            'title' => 'Ibadat Komunitas',
            'start_date' => $later,
            'start_time' => '09:00',
            'location' => 'Kapel',
            'description' => 'Doa bersama.',
        ])->assertRedirect(route('agenda.index'));

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'bimbingan',
            'mentor_name' => 'Br. Yohanes Uji, FIC',
            'meeting_date' => $sooner,
            'meeting_time' => '15:30',
            'location' => 'Ruang Bimbingan',
            'topic_notes' => 'Doa batin.',
        ])->assertRedirect(route('agenda.index'));

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'wawanhati',
            'local_leader_name' => 'Br. Piko Uji, FIC',
            'meeting_date' => $sooner,
            'meeting_time' => '08:00:00',
            'location' => 'Ruang Piko',
        ])->assertRedirect(route('agenda.index'));

        $bimbingan = SpiritualGuidanceSchedule::first();
        $this->assertSame('15:30:00', $bimbingan->scheduled_at->format('H:i:s'));
        $this->assertSame('08:00:00', WawanhatiSchedule::first()->scheduled_at->format('H:i:s'));
        $this->assertSame('Ibadat Komunitas', PersonalAgenda::first()->title);

        $soonerLabel = Carbon::parse($sooner)->locale('id')->translatedFormat('j F Y');

        $this->actingAs($user)
            ->get(route('agenda.index'))
            ->assertOk()
            ->assertSee($soonerLabel, false)
            ->assertDontSee('26262626')
            ->assertSeeInOrder([
                'Wawanhati Pimpinan Lokal',
                'Bimbingan Rohani',
                'Ibadat Komunitas',
            ]);

        $this->actingAs($user)
            ->get(route('kalender', ['date' => $sooner]))
            ->assertOk()
            ->assertSee('calendar-day selected', false)
            ->assertDontSee('color: inherit', false)
            ->assertSee('Br. Yohanes Uji, FIC')
            ->assertSee('Br. Piko Uji, FIC')
            ->assertDontSee('Ibadat Komunitas');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Br. Piko Uji, FIC')
            ->assertSee('0 / 2');
    }

    public function test_owner_can_edit_and_delete_personal_agendas(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $today = now()->toDateString();

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'bimbingan',
            'mentor_name' => 'Br. Yohanes Uji, FIC',
            'meeting_date' => $today,
            'meeting_time' => '10:00',
            'location' => 'Ruang Bimbingan',
            'topic_notes' => 'Doa batin.',
        ])->assertRedirect(route('agenda.index'));

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'wawanhati',
            'local_leader_name' => 'Br. Piko Uji, FIC',
            'meeting_date' => $today,
            'meeting_time' => '14:00',
            'location' => 'Ruang Piko',
        ])->assertRedirect(route('agenda.index'));

        $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'lainnya',
            'title' => 'Ibadat Komunitas',
            'start_date' => $today,
            'start_time' => '09:00',
            'location' => 'Kapel',
            'description' => 'Doa bersama.',
        ])->assertRedirect(route('agenda.index'));

        $bimbingan = SpiritualGuidanceSchedule::first();
        $wawanhati = WawanhatiSchedule::first();
        $lainnya = PersonalAgenda::first();

        $this->actingAs($user)
            ->get(route('agenda.index'))
            ->assertOk()
            ->assertSee('agenda/bimbingan/'.$bimbingan->id.'/edit', false)
            ->assertSee('agenda/wawanhati/'.$wawanhati->id.'/edit', false)
            ->assertSee('agenda/lainnya/'.$lainnya->id.'/edit', false);

        $this->actingAs($other)
            ->get(route('agenda.edit', ['type' => 'bimbingan', 'schedule' => $bimbingan->id]))
            ->assertNotFound();
        $this->actingAs($other)
            ->put(route('agenda.update', ['type' => 'wawanhati', 'schedule' => $wawanhati->id]), [
                'local_leader_name' => 'Br. Orang Lain, FIC',
                'meeting_date' => $today,
                'meeting_time' => '16:00',
                'location' => 'Ruang Lain',
            ])
            ->assertNotFound();
        $this->actingAs($other)
            ->delete(route('agenda.destroy', ['type' => 'lainnya', 'schedule' => $lainnya->id]))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('agenda.edit', ['type' => 'bimbingan', 'schedule' => $bimbingan->id]))
            ->assertOk()
            ->assertSee('Ubah Jadwal Bimbingan Rohani')
            ->assertSee('Br. Yohanes Uji, FIC')
            ->assertSee('value="10:00"', false);

        $this->actingAs($user)
            ->put(route('agenda.update', ['type' => 'bimbingan', 'schedule' => $bimbingan->id]), [
                'meeting_date' => $today,
                'meeting_time' => '11:15',
                'location' => 'Ruang Baru',
            ])
            ->assertSessionHasErrors('mentor_name');

        $this->actingAs($user)
            ->put(route('agenda.update', ['type' => 'bimbingan', 'schedule' => $bimbingan->id]), [
                'mentor_name' => 'Br. Markus Baru, FIC',
                'meeting_date' => $today,
                'meeting_time' => '11:15',
                'location' => 'Ruang Baru',
                'topic_notes' => 'Discernment.',
            ])
            ->assertRedirect(route('agenda.index'));

        $bimbingan->refresh();
        $this->assertSame('Br. Markus Baru, FIC', $bimbingan->mentor_name);
        $this->assertSame('11:15:00', $bimbingan->scheduled_at->format('H:i:s'));
        $this->assertSame('scheduled', $bimbingan->status);
        $this->assertSame(1, SpiritualGuidanceSchedule::count());

        $this->actingAs($user)
            ->put(route('agenda.update', ['type' => 'lainnya', 'schedule' => $lainnya->id]), [
                'title' => 'Rekoleksi Distrik',
                'start_date' => $today,
                'start_time' => '08:30',
                'location' => 'Aula',
                'description' => 'Persiapan pakaian.',
            ])
            ->assertRedirect(route('agenda.index'));

        $this->assertSame('Rekoleksi Distrik', $lainnya->fresh()->title);
        $this->assertSame('08:30:00', $lainnya->fresh()->start_at->format('H:i:s'));

        $this->actingAs($user)
            ->delete(route('agenda.destroy', ['type' => 'wawanhati', 'schedule' => $wawanhati->id]))
            ->assertRedirect(route('agenda.index'));

        $this->assertSoftDeleted('wawanhati_schedules', ['id' => $wawanhati->id]);

        $this->actingAs($user)
            ->get(route('agenda.index'))
            ->assertOk()
            ->assertSee('Br. Markus Baru, FIC')
            ->assertSee('Rekoleksi Distrik')
            ->assertSee('Agenda berhasil dihapus dari jadwal Anda.')
            ->assertDontSee('Br. Piko Uji, FIC');

        $this->actingAs($user)
            ->get(route('kalender', ['date' => $today]))
            ->assertOk()
            ->assertSee('Br. Markus Baru, FIC')
            ->assertDontSee('Br. Piko Uji, FIC');
    }

    public function test_journal_is_published_and_counts_on_the_dashboard(): void
    {
        $user = User::factory()->create();
        $reflection = $this->narrative(240);

        $this->actingAs($user)->post(route('jurnal.store'), [
            'activity_date' => now()->toDateString(),
            'activity_name' => 'Lokakarya Uji Data',
            'organizer' => 'Dewan Provinsi',
            'speaker' => 'Rm. Uji',
            'description' => 'Latihan pencatatan jurnal pengembangan diri.',
            'reflection' => $reflection,
        ])->assertRedirect(route('jurnal.index'));

        $journal = SelfDevelopmentJournal::first();
        $this->assertSame('published', $journal->status);
        $this->assertNotNull($journal->published_at);

        $this->actingAs($user)
            ->get(route('jurnal.index'))
            ->assertOk()
            ->assertSee('Lokakarya Uji Data')
            ->assertSee('Dipublikasikan');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Target Pembinaan Bulanan')
            ->assertSee('1 jurnal tercatat bulan ini')
            ->assertSee('24 (2', false);
    }

    public function test_owner_can_edit_and_delete_journal(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $journal = SelfDevelopmentJournal::create([
            'user_id' => $user->id,
            'activity_date' => now()->toDateString(),
            'activity_name' => 'Retret Awal',
            'organizer' => 'Provinsi FIC',
            'speaker' => 'Rm. Robert',
            'description' => 'Materi awal retret.',
            'reflection' => $this->narrative(250),
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Form edit
        $this->actingAs($user)->get(route('jurnal.edit', $journal))
            ->assertOk()
            ->assertSee('Edit Jurnal Pengembangan Diri')
            ->assertSee('Retret Awal');

        // Orang lain tidak bisa akses form edit
        $this->actingAs($otherUser)->get(route('jurnal.edit', $journal))
            ->assertNotFound();

        // Update jurnal
        $newReflection = $this->narrative(280);
        $this->actingAs($user)->put(route('jurnal.update', $journal), [
            'activity_date' => now()->addDay()->toDateString(),
            'activity_name' => 'Retret Diperbarui',
            'organizer' => 'Provinsi FIC Indonesia',
            'speaker' => 'Rm. Thomas',
            'description' => 'Materi baru retret.',
            'reflection' => $newReflection,
        ])->assertRedirect(route('jurnal.index'))
          ->assertSessionHas('success', 'Jurnal pengembangan diri berhasil diperbarui.');

        $this->assertSame('Retret Diperbarui', $journal->fresh()->activity_name);
        $this->assertSame('Rm. Thomas', $journal->fresh()->speaker);

        // Orang lain tidak bisa update
        $this->actingAs($otherUser)->put(route('jurnal.update', $journal), [
            'activity_date' => now()->toDateString(),
            'activity_name' => 'Retret Bajakan',
            'organizer' => 'Provinsi FIC',
            'speaker' => 'Rm. Lain',
            'description' => 'Deskripsi',
            'reflection' => $newReflection,
        ])->assertNotFound();

        // Hapus jurnal
        $this->actingAs($user)->delete(route('jurnal.destroy', $journal))
            ->assertRedirect(route('jurnal.index'))
            ->assertSessionHas('success', 'Jurnal pengembangan diri berhasil dihapus.');

        $this->assertSoftDeleted('self_development_journals', ['id' => $journal->id]);

        // Orang lain tidak bisa hapus
        $otherJournal = SelfDevelopmentJournal::create([
            'user_id' => $user->id,
            'activity_date' => now()->toDateString(),
            'activity_name' => 'Jurnal Kedua',
            'organizer' => 'Provinsi FIC',
            'speaker' => 'Rm. Robert',
            'description' => 'Materi',
            'reflection' => $this->narrative(220),
            'status' => 'published',
            'published_at' => now(),
        ]);
        $this->actingAs($otherUser)->delete(route('jurnal.destroy', $otherJournal))
            ->assertNotFound();
    }

    public function test_bimbingan_and_wawanhati_reject_future_dates(): void
    {
        $user = User::factory()->create();
        $futureDate = now()->addDay()->toDateString();
        $content = $this->narrative();

        // Bimbingan reject future date
        $this->actingAs($user)->post(route('bimbingan.store'), [
            'meeting_date' => $futureDate,
            'mentor_name' => 'Br. Markus, FIC',
            'location' => 'Ruang Bimbingan',
            'report_content' => $content,
        ])->assertSessionHasErrors('meeting_date');

        $this->assertSame(0, SpiritualGuidanceReport::count());

        // Wawanhati reject future date
        $this->actingAs($user)->post(route('wawanhati.store'), [
            'meeting_date' => $futureDate,
            'local_leader_name' => 'Br. Frans, FIC',
            'location' => 'Ruang Piko',
            'report_content' => $content,
        ])->assertSessionHasErrors('meeting_date');

        $this->assertSame(0, WawanhatiReport::count());
    }

    public function test_owner_can_edit_and_delete_bimbingan_report(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $content = $this->narrative();

        $report = SpiritualGuidanceReport::create([
            'user_id' => $user->id,
            'mentor_name' => 'Br. Awal, FIC',
            'meeting_at' => now()->toDateString(),
            'location' => 'Ruang A',
            'report_content' => $content,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Form edit
        $this->actingAs($user)->get(route('bimbingan.edit', $report))
            ->assertOk()
            ->assertSee('Edit Laporan Bimbingan Rohani')
            ->assertSee('Br. Awal, FIC');

        $this->actingAs($other)->get(route('bimbingan.edit', $report))
            ->assertNotFound();

        // Update
        $newContent = $this->narrative(260);
        $this->actingAs($user)->put(route('bimbingan.update', $report), [
            'meeting_date' => now()->toDateString(),
            'mentor_name' => 'Br. Diperbarui, FIC',
            'location' => 'Ruang Baru',
            'report_content' => $newContent,
        ])->assertRedirect(route('bimbingan.index'))
          ->assertSessionHas('success', 'Laporan Bimbingan Rohani berhasil diperbarui.');

        $this->assertSame('Br. Diperbarui, FIC', $report->fresh()->mentor_name);

        // Delete
        $this->actingAs($user)->delete(route('bimbingan.destroy', $report))
            ->assertRedirect(route('bimbingan.index'))
            ->assertSessionHas('success', 'Laporan Bimbingan Rohani berhasil dihapus.');

        $this->assertSoftDeleted('spiritual_guidance_reports', ['id' => $report->id]);
    }

    public function test_owner_can_edit_and_delete_wawanhati_report(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $content = $this->narrative();

        $report = WawanhatiReport::create([
            'user_id' => $user->id,
            'local_leader_name' => 'Br. Piko Awal, FIC',
            'meeting_at' => now()->toDateString(),
            'location' => 'Ruang Piko',
            'report_content' => $content,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Form edit
        $this->actingAs($user)->get(route('wawanhati.edit', $report))
            ->assertOk()
            ->assertSee('Edit Laporan Wawanhati Pimpinan Lokal')
            ->assertSee('Br. Piko Awal, FIC');

        $this->actingAs($other)->get(route('wawanhati.edit', $report))
            ->assertNotFound();

        // Update
        $newContent = $this->narrative(260);
        $this->actingAs($user)->put(route('wawanhati.update', $report), [
            'meeting_date' => now()->toDateString(),
            'local_leader_name' => 'Br. Piko Diperbarui, FIC',
            'location' => 'Ruang Piko Baru',
            'report_content' => $newContent,
        ])->assertRedirect(route('wawanhati.index'))
          ->assertSessionHas('success', 'Laporan Wawanhati Pimpinan Lokal berhasil diperbarui.');

        $this->assertSame('Br. Piko Diperbarui, FIC', $report->fresh()->local_leader_name);

        // Delete
        $this->actingAs($user)->delete(route('wawanhati.destroy', $report))
            ->assertRedirect(route('wawanhati.index'))
            ->assertSessionHas('success', 'Laporan Wawanhati Pimpinan Lokal berhasil dihapus.');

        $this->assertSoftDeleted('wawanhati_reports', ['id' => $report->id]);
    }

    private function narrative(int $length = 220): string
    {
        return mb_substr(str_repeat('Refleksi pembinaan bruder muda tetap ditulis utuh. ', 20), 0, $length);
    }

    private function tinyJpeg(): string
    {
        return base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
    }
}
