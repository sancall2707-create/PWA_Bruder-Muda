<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_shows_stored_reports_with_real_status(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);
        $budi = User::factory()->create(['role' => 'user', 'name' => 'Br. Budi Pantau']);

        $this->journal($anton, 'Lokakarya Pantau', '2026-10-15', 'published');
        $this->bimbingan($budi, '2026-10-16 09:00:00', 'draft');

        $this->actingAs($admin)
            ->get(route('superadmin.monitoring.index', ['bulan' => 10, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee('Br. Anton Pantau')
            ->assertSee('Br. Budi Pantau')
            ->assertSee('Lokakarya Pantau')
            ->assertSee('Jurnal Pengembangan Diri')
            ->assertSee('Bimbingan Rohani')
            ->assertSee('15 Oktober 2026')
            ->assertSee('Selesai')
            ->assertSee('Menunggu')
            ->assertDontSee('Dipublikasi')
            ->assertDontSee('Terkirim');
    }

    public function test_bruder_and_month_filters_hide_other_rows(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);
        $budi = User::factory()->create(['role' => 'user', 'name' => 'Br. Budi Pantau']);

        $this->journal($anton, 'Jurnal Oktober Anton', '2026-10-15', 'published');
        $this->journal($budi, 'Jurnal Oktober Budi', '2026-10-18', 'published');
        $this->journal($anton, 'Jurnal September Anton', '2026-09-02', 'published');

        $this->actingAs($admin)
            ->get(route('superadmin.monitoring.index', [
                'bulan' => 10,
                'tahun' => 2026,
                'bruder' => $anton->id,
            ]))
            ->assertOk()
            ->assertSee('Jurnal Oktober Anton')
            ->assertDontSee('Jurnal Oktober Budi')
            ->assertDontSee('Jurnal September Anton');

        $this->actingAs($admin)
            ->get(route('superadmin.monitoring.index', [
                'bulan' => 10,
                'tahun' => 2026,
                'bruder' => $admin->id,
            ]))
            ->assertOk()
            ->assertSee('Jurnal Oktober Anton')
            ->assertSee('Jurnal Oktober Budi');
    }

    public function test_agenda_recap_lists_all_three_schedule_types(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);

        SpiritualGuidanceSchedule::query()->create([
            'user_id' => $anton->id,
            'mentor_name' => 'Rm. Mentor Pantau',
            'scheduled_at' => '2026-10-15 16:00:00',
            'location' => 'Kapel Uji',
            'status' => 'scheduled',
        ]);
        WawanhatiSchedule::query()->create([
            'user_id' => $anton->id,
            'local_leader_name' => 'Br. Pimlok Pantau',
            'scheduled_at' => '2026-10-20 08:30:00',
            'location' => 'Rumah Komunitas',
            'status' => 'scheduled',
        ]);
        PersonalAgenda::query()->create([
            'user_id' => $anton->id,
            'title' => 'Retret Pribadi Pantau',
            'start_at' => '2026-10-22 05:00:00',
            'location' => 'Aula',
            'status' => 'scheduled',
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.monitoring.agenda', ['bulan' => 10, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee('Br. Anton Pantau')
            ->assertSee('Bimbingan Rohani')
            ->assertSee('15 Oktober 2026')
            ->assertSee('16:00 WIB')
            ->assertSee('Rm. Mentor Pantau')
            ->assertSee('Kapel Uji')
            ->assertSee('Wawanhati Pilok')
            ->assertSee('08:30 WIB')
            ->assertSee('Retret Pribadi Pantau')
            ->assertSee('Menunggu');

        $excel = $this->actingAs($admin)->get(route('superadmin.monitoring.export', [
            'bulan' => 10,
            'tahun' => 2026,
            'jenis' => 'agenda',
            'format' => 'excel',
        ]));
        $excel->assertOk();
        $sheet = $excel->getContent();
        $this->assertStringContainsString('Rm. Mentor Pantau', $sheet);
        $this->assertStringContainsString('16:00 WIB', $sheet);
        $this->assertStringContainsString('ss:Bold="1"', $sheet);
        $this->assertStringContainsString('ss:Horizontal="Center"', $sheet);
        $this->assertStringContainsString('ss:StyleID="Header"', $sheet);
        $this->assertStringContainsString('<Column ss:AutoFitWidth="0" ss:Width="130"/>', $sheet);
    }

    public function test_agenda_calendar_shows_the_selected_day(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);

        PersonalAgenda::query()->create([
            'user_id' => $anton->id,
            'title' => 'Retret Pribadi Pantau',
            'start_at' => '2026-10-15 05:00:00',
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.monitoring.agenda', [
                'bulan' => 10,
                'tahun' => 2026,
                'tampilan' => 'kalender',
                'tanggal' => '2026-10-15',
            ]))
            ->assertOk()
            ->assertSee('calendar-day selected', false)
            ->assertSee('Retret Pribadi Pantau')
            ->assertSee('15 Oktober 2026')
            ->assertSee('Selesai');
    }

    public function test_excel_export_follows_the_active_filter(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);
        $budi = User::factory()->create(['role' => 'user', 'name' => 'Br. Budi Pantau']);

        $this->journal($anton, 'Jurnal Oktober Anton', '2026-10-15', 'published');
        $this->journal($budi, 'Jurnal Oktober Budi', '2026-10-18', 'published');

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.export', [
            'bulan' => 10,
            'tahun' => 2026,
            'bruder' => $anton->id,
            'jenis' => 'laporan',
            'format' => 'excel',
        ]));

        $response->assertOk();
        $response->assertDownload('rekap-laporan-2026-10.xls');
        $body = $response->getContent();
        $this->assertStringContainsString('Jurnal Oktober Anton', $body);
        $this->assertStringContainsString('Br. Anton Pantau', $body);
        $this->assertStringContainsString('Selesai', $body);
        $this->assertStringNotContainsString('Br. Budi Pantau', $body);
        $this->assertStringContainsString('<Workbook', $body);
        $this->assertStringContainsString('ss:Bold="1"', $body);
        $this->assertStringContainsString('ss:Horizontal="Center"', $body);
        $this->assertStringContainsString('<Column ss:AutoFitWidth="0" ss:Width="140"/>', $body);
    }

    public function test_pdf_export_includes_filtered_agenda(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);

        PersonalAgenda::query()->create([
            'user_id' => $anton->id,
            'title' => 'Retret Pribadi Pantau',
            'start_at' => '2026-10-15 05:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.export', [
            'bulan' => 10,
            'tahun' => 2026,
            'jenis' => 'agenda',
            'format' => 'pdf',
        ]));

        $response->assertOk();
        $response->assertDownload('rekap-agenda-2026-10.pdf');
        $body = $response->getContent();
        $this->assertStringStartsWith('%PDF-1.4', $body);
        $this->assertStringContainsString('Br. Anton Pantau', $body);
        $this->assertStringContainsString('Retret Pribadi Pantau', $body);
    }

    public function test_bruder_cannot_open_monitoring_or_export(): void
    {
        $bruder = User::factory()->create(['role' => 'user']);

        $this->actingAs($bruder)->get(route('superadmin.monitoring.index'))->assertForbidden();
        $this->actingAs($bruder)->get(route('superadmin.monitoring.agenda'))->assertForbidden();
        $this->actingAs($bruder)->get(route('superadmin.monitoring.export'))->assertForbidden();
    }

    public function test_monitoring_supports_month_range_filter(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $anton = User::factory()->create(['role' => 'user', 'name' => 'Br. Anton Pantau']);

        $this->journal($anton, 'Jurnal Januari', '2026-01-10', 'published');
        $this->journal($anton, 'Jurnal April', '2026-04-15', 'published');
        $this->journal($anton, 'Jurnal Agustus', '2026-08-20', 'published');
        $this->journal($anton, 'Jurnal Desember', '2026-12-05', 'published');

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.index', [
            'bulan' => 1,
            'bulan_akhir' => 8,
            'tahun' => 2026,
        ]));

        $response->assertOk()
            ->assertSee('Januari – Agustus 2026')
            ->assertSee('Jurnal Januari')
            ->assertSee('Jurnal April')
            ->assertSee('Jurnal Agustus')
            ->assertDontSee('Jurnal Desember');
    }

    public function test_agenda_status_automatically_done_when_report_is_submitted(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create(['role' => 'user', 'name' => 'Br. Renungan FIC']);

        // Buat jadwal bimbingan
        \App\Models\SpiritualGuidanceSchedule::create([
            'user_id' => $bruder->id,
            'mentor_name' => 'Br. Pembimbing FIC',
            'scheduled_at' => '2026-10-20 10:00:00',
            'location' => 'Ruang Bimbingan',
            'status' => 'scheduled',
        ]);

        // Sebelum laporan dikirim, agenda berstatus Menunggu
        $this->actingAs($admin)->get(route('superadmin.monitoring.agenda', ['bulan' => 10, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee('Menunggu');

        // Bruder mengirimkan renungan / laporan
        $this->actingAs($bruder)->post(route('bimbingan.store'), [
            'meeting_date' => '2026-10-20',
            'mentor_name' => 'Br. Pembimbing FIC',
            'location' => 'Ruang Bimbingan',
            'report_content' => str_repeat('Refleksi bimbingan rohani yang mendalam untuk pembinaan bruder muda FIC. ', 5),
        ])->assertRedirect(route('bimbingan.index'));

        // Setelah renungan terkirim, agenda otomatis berstatus Selesai
        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.agenda', ['bulan' => 10, 'tahun' => 2026]));
        $response->assertOk()
            ->assertSee('Selesai');
    }

    public function test_super_admin_can_view_raport_pendampingan_bruder_muda(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $bruder = User::factory()->create([
            'role' => 'user',
            'name' => 'Br. Yohanes Subagio, FIC, S.Pd.',
            'username' => 'yohanes_subagio',
            'birth_place' => 'Sleman, D.I. Yogyakarta',
            'family_data' => [
                'parish_origin' => 'Paroki St. Petrus & Paulus, Babadan',
                'father_name' => 'Antonius Sudarsono Lelono',
                'mother_name' => 'Monika Maria Goretti',
                'older_siblings_count' => '1',
                'younger_siblings_count' => '2',
            ],
            'education_data' => [
                'sd_name' => 'SD Kanisius Babadan',
                'sd_year' => '2010',
            ],
            'formation_data' => [
                'postulat_in' => '2021',
                'postulat_count' => '5',
                'postulat_magister' => 'Br. Agus, FIC (Magister)',
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('superadmin.monitoring.raport', [
            'bruder' => $bruder->id,
            'bulan' => 8,
            'bulan_akhir' => 10,
            'tahun' => 2026,
        ]));

        $response->assertOk()
            ->assertSee('RAPORT PENDAMPINGAN BRUDER MUDA')
            ->assertSee('Kongregasi Bruder FIC Indonesia')
            ->assertSee('Tim Pemantap Panggilan Bruder Muda (TPPBM)')
            ->assertSee('Br. Yohanes Subagio, FIC, S.Pd.')
            ->assertSee('yohanes_subagio')
            ->assertSee('Sleman, D.I. Yogyakarta')
            ->assertSee('Paroki St. Petrus & Paulus, Babadan')
            ->assertSee('Antonius Sudarsono Lelono')
            ->assertSee('Monika Maria Goretti')
            ->assertSee('SD Kanisius Babadan')
            ->assertSee('Postulat')
            ->assertSee('Br. F.A. Galih Sih Hartanta, FIC')
            ->assertSee('Br. Andrias Purwanto, FIC');
    }

    private function journal(User $user, string $name, string $date, string $status): void
    {
        SelfDevelopmentJournal::query()->create([
            'user_id' => $user->id,
            'activity_date' => $date,
            'activity_name' => $name,
            'organizer' => 'Komunitas Uji',
            'description' => 'Catatan kegiatan pantau untuk monitoring.',
            'reflection' => 'Refleksi singkat kegiatan pantau.',
            'status' => $status,
            'published_at' => $status === 'published' ? $date.' 12:00:00' : null,
        ]);
    }

    private function bimbingan(User $user, string $meetingAt, string $status): void
    {
        SpiritualGuidanceReport::query()->create([
            'user_id' => $user->id,
            'mentor_name' => 'Rm. Mentor Pantau',
            'meeting_at' => $meetingAt,
            'location' => 'Kapel Uji',
            'report_content' => str_repeat('Catatan bimbingan pantau. ', 12),
            'status' => $status,
            'submitted_at' => $meetingAt,
        ]);
    }
}
