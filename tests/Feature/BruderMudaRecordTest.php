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
            ->assertSee('0 / 3');
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
            ->assertSee('1 / 3')
            ->assertSee('33%');
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
