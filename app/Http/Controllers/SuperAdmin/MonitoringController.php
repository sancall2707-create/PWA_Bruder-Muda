<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BruderNote;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Services\MonitoringExport;
use App\Services\MonitoringQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MonitoringController extends Controller
{
    public function __construct(
        private readonly MonitoringQuery $monitoring,
        private readonly MonitoringExport $exporter,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->monitoring->filters($request);

        return view('super-admin.monitoring.index', [
            'filters' => $filters,
            'bruders' => $this->bruders(),
            'reports' => $this->monitoring->reports($filters),
            'headerTitle' => 'Monitoring Laporan',
            'headerSubtitle' => $filters['period_label'],
        ]);
    }

    public function agenda(Request $request): View
    {
        $filters = $this->monitoring->filters($request);
        $agendas = $this->monitoring->agendas($filters);
        $tampilan = $request->input('tampilan') === 'kalender' ? 'kalender' : 'daftar';

        return view('super-admin.monitoring.agenda', [
            'filters' => $filters,
            'bruders' => $this->bruders(),
            'agendas' => $agendas,
            'tampilan' => $tampilan,
            'calendar' => $this->monitoring->calendar($agendas, $filters['tahun'], $filters['bulan'], $request->input('tanggal')),
            'headerTitle' => 'Rekapitulasi Agenda',
            'headerSubtitle' => $filters['period_label'],
        ]);
    }

    public function export(Request $request): Response
    {
        $filters = $this->monitoring->filters($request);
        $kind = $request->input('jenis') === 'agenda' ? 'agenda' : 'laporan';
        $format = $request->input('format') === 'pdf' ? 'pdf' : 'excel';
        $rows = $kind === 'agenda'
            ? $this->monitoring->agendas($filters)
            : $this->monitoring->reports($filters);

        $filename = $this->exporter->filename($kind, $format, $filters);
        $body = $format === 'pdf'
            ? $this->exporter->pdf($kind, $filters, $rows)
            : $this->exporter->excel($kind, $filters, $rows);

        $contentType = $format === 'pdf'
            ? 'application/pdf'
            : 'application/vnd.ms-excel; charset=UTF-8';

        return response($body, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function raport(Request $request): View
    {
        $filters = $this->monitoring->filters($request);
        $bruders = $this->bruders();

        $selectedBruderId = $filters['bruder'] ?: optional($bruders->first())->id;
        $selectedBruder = $selectedBruderId ? User::find($selectedBruderId) : null;

        $raport = $selectedBruder ? $this->buildRaportData($selectedBruder, $filters) : null;

        return view('super-admin.monitoring.raport', [
            'filters' => $filters,
            'bruders' => $bruders,
            'selectedBruder' => $selectedBruder,
            'raport' => $raport,
            'headerTitle' => 'Raport Pendampingan',
            'headerSubtitle' => $selectedBruder ? $selectedBruder->name : 'Pilih Bruder Muda',
        ]);
    }

    private function buildRaportData(User $user, array $filters): array
    {
        $tahun = (int) $filters['tahun'];
        $bulanAwal = (int) $filters['bulan'];
        $bulanAkhir = (int) ($filters['bulan_akhir'] ?? $bulanAwal);

        // 1. Data Diri & Akun
        $dataDiri = [
            'nama' => $user->name,
            'username' => $user->username,
            'email' => $user->email ?: '-',
            'birth_date' => $user->birth_date ? Carbon::parse($user->birth_date)->locale('id')->translatedFormat('d F Y') : '-',
            'birth_place' => $user->birth_place ?: '-',
            'phone' => $user->phone ?: '-',
            'parish_origin' => $user->family_data['parish_origin'] ?? '-',
            'home_address' => $user->family_data['home_address'] ?? '-',
        ];

        // 2. Data Keluarga & Kontak Darurat
        $family = $user->family_data ?? [];
        $olderCount = (int) ($family['older_siblings_count'] ?? 0);
        $youngerCount = (int) ($family['younger_siblings_count'] ?? 0);
        $totalSiblings = $olderCount + $youngerCount + 1;
        $childOrder = $olderCount + 1;
        $siblingsText = ($olderCount || $youngerCount)
            ? "{$olderCount} Kakak, {$youngerCount} Adik (Anak ke-{$childOrder} dari {$totalSiblings} bersaudara)"
            : '-';

        $dataKeluarga = [
            'father_name' => $family['father_name'] ?? '-',
            'father_birth_year' => $family['father_birth_year'] ?? '-',
            'father_job' => $family['father_job'] ?? '-',
            'mother_name' => $family['mother_name'] ?? '-',
            'mother_birth_year' => $family['mother_birth_year'] ?? '-',
            'mother_job' => $family['mother_job'] ?? '-',
            'siblings_text' => $siblingsText,
            'emergency_name' => $family['emergency_name'] ?? '-',
            'emergency_relation' => $family['emergency_relation'] ?? '-',
            'emergency_phone' => $family['emergency_phone'] ?? '-',
            'emergency_address' => $family['emergency_address'] ?? '-',
        ];

        // 3. Riwayat Pendidikan
        $edu = $user->education_data ?? [];
        $pendidikan = [
            [
                'jenjang' => 'SD',
                'nama' => $edu['sd_name'] ?? '-',
                'jurusan' => '-',
                'tahun' => ! empty($edu['sd_year']) ? 'Lulus ' . $edu['sd_year'] : '-',
            ],
            [
                'jenjang' => 'SMP',
                'nama' => $edu['smp_name'] ?? '-',
                'jurusan' => '-',
                'tahun' => ! empty($edu['smp_year']) ? 'Lulus ' . $edu['smp_year'] : '-',
            ],
            [
                'jenjang' => 'SMA/K',
                'nama' => $edu['sma_name'] ?? '-',
                'jurusan' => ! empty($edu['sma_major']) ? '(' . $edu['sma_major'] . ')' : '-',
                'tahun' => ! empty($edu['sma_year']) ? 'Lulus ' . $edu['sma_year'] : '-',
            ],
            [
                'jenjang' => 'S1',
                'nama' => $edu['s1_name'] ?? 'Belum menempuh',
                'jurusan' => $edu['s1_major'] ?? '-',
                'tahun' => ! empty($edu['s1_in']) ? 'Masuk: ' . $edu['s1_in'] . (! empty($edu['s1_out']) ? ' | Lulus: ' . $edu['s1_out'] : (! empty($edu['s1_target']) ? ' | Target: ' . $edu['s1_target'] : '')) : '-',
            ],
            [
                'jenjang' => 'S2',
                'nama' => $edu['s2_name'] ?? 'Belum menempuh',
                'jurusan' => $edu['s2_major'] ?? '-',
                'tahun' => ! empty($edu['s2_in']) ? 'Masuk: ' . $edu['s2_in'] . (! empty($edu['s2_out']) ? ' | Lulus: ' . $edu['s2_out'] : (! empty($edu['s2_target']) ? ' | Target: ' . $edu['s2_target'] : '')) : '-',
            ],
        ];

        // 4. Formasi Religius FIC
        $form = $user->formation_data ?? [];
        $formasi = [
            [
                'tahapan' => 'Postulat',
                'tahun' => $form['postulat_in'] ?? '-',
                'angkatan' => ! empty($form['postulat_count']) ? $form['postulat_count'] . ' Frater/Bruder' : '-',
                'magister' => $form['postulat_magister'] ?? '-',
            ],
            [
                'tahapan' => 'Novisiat Kanonik',
                'tahun' => $form['kanonik_in'] ?? '-',
                'angkatan' => ! empty($form['kanonik_count']) ? $form['kanonik_count'] . ' Frater/Bruder' : '-',
                'magister' => $form['kanonik_magister'] ?? '-',
            ],
            [
                'tahapan' => 'Novisiat Apostolik',
                'tahun' => $form['apostolik_in'] ?? '-',
                'angkatan' => ! empty($form['apostolik_count']) ? $form['apostolik_count'] . ' Frater/Bruder' : '-',
                'magister' => $form['apostolik_magister'] ?? '-',
            ],
            [
                'tahapan' => 'Prasetia Pertama',
                'tahun' => $form['prasetia_1_year'] ?? '-',
                'angkatan' => ! empty($form['prasetia_1_count']) ? $form['prasetia_1_count'] . ' Bruder' : '-',
                'magister' => $form['prasetia_1_provincial'] ?? '-',
            ],
        ];

        $pembaruanKe = [1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga', 4 => 'Keempat', 5 => 'Kelima', 6 => 'Keenam'];
        $hasPembaruan = false;
        for ($p = 1; $p <= 6; $p++) {
            $pemData = $form['pembaruan'][$p] ?? [];
            if (! empty($pemData['year'])) {
                $hasPembaruan = true;
                $formasi[] = [
                    'tahapan' => 'Pembaruan ' . $pembaruanKe[$p],
                    'tahun' => $pemData['year'],
                    'angkatan' => ! empty($pemData['count']) ? $pemData['count'] . ' Bruder' : '-',
                    'magister' => $pemData['provincial'] ?? '-',
                ];
            }
        }
        if (! $hasPembaruan) {
            $formasi[] = [
                'tahapan' => 'Pembaruan Ketiga - Keenam',
                'tahun' => 'Dalam masa persiapan / Belum terlaksana',
                'angkatan' => '-',
                'magister' => '-',
            ];
        }

        // 5. Komunitas & Perutusan
        $asg = $user->assignment_data ?? [];
        $tugasKe = [1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga', 4 => 'Keempat', 5 => 'Kelima', 6 => 'Keenam'];
        $perutusan = [];
        for ($t = 1; $t <= 6; $t++) {
            $tData = $asg[$t] ?? [];
            if (! empty($tData['community'])) {
                $masa = trim(($tData['start_year'] ?? '') . ' - ' . ($tData['end_year'] ?? 'Sekarang'));
                $perutusan[] = [
                    'tugas_ke' => $tugasKe[$t],
                    'komunitas' => $tData['community'],
                    'masa' => $masa !== '-' ? $masa : '-',
                    'pokok' => $tData['duty'] ?? '-',
                ];
            }
        }
        if (empty($perutusan)) {
            $perutusan[] = [
                'tugas_ke' => 'Pertama',
                'komunitas' => $user->community_location ?: 'Komunitas FIC',
                'masa' => '2025 - Sekarang',
                'pokok' => 'Penugasan Komunitas',
            ];
        }

        // 6. Laporan Pelaksanaan Bimbingan Rohani per Bulan
        $bimbinganBulan = [];
        $bimbinganReports = $user->spiritualGuidanceReports()
            ->whereYear('meeting_at', $tahun)
            ->get();

        for ($m = $bulanAwal; $m <= $bulanAkhir; $m++) {
            $monthName = mb_strtoupper(Carbon::create($tahun, $m, 1)->locale('id')->translatedFormat('F'));
            $rep = $bimbinganReports->first(fn ($r) => (int) $r->meeting_at->format('n') === $m);

            $bimbinganBulan[] = [
                'bulan' => $monthName,
                'done' => (bool) $rep,
                'date' => $rep ? $rep->meeting_at->format('d') : '-',
                'mentor' => $rep ? $rep->mentor_name : 'Br. Yohanes Subagio, FIC',
                'location' => $rep ? ($rep->location ?: '-') : '-',
                'content' => $rep ? $rep->report_content : '-',
            ];
        }

        // 7. Laporan Pelaksanaan Wawanhati Pimpinan Lokal per Bulan
        $wawanhatiBulan = [];
        $wawanhatiReports = $user->wawanhatiReports()
            ->whereYear('meeting_at', $tahun)
            ->get();

        for ($m = $bulanAwal; $m <= $bulanAkhir; $m++) {
            $monthName = mb_strtoupper(Carbon::create($tahun, $m, 1)->locale('id')->translatedFormat('F'));
            $rep = $wawanhatiReports->first(fn ($r) => (int) $r->meeting_at->format('n') === $m);

            $wawanhatiBulan[] = [
                'bulan' => $monthName,
                'done' => (bool) $rep,
                'date' => $rep ? $rep->meeting_at->format('d') : '-',
                'leader' => $rep ? $rep->local_leader_name : 'Br. Yohanes Apaelagei, FIC',
                'location' => $rep ? ($rep->location ?: '-') : '-',
                'content' => $rep ? $rep->report_content : '-',
            ];
        }

        // 8. Laporan Kegiatan Pengembangan Diri
        $journals = $user->selfDevelopmentJournals()
            ->whereYear('activity_date', $tahun)
            ->whereBetween(DB::raw("CAST(strftime('%m', activity_date) AS INTEGER)"), [$bulanAwal, $bulanAkhir])
            ->orderBy('activity_date')
            ->get();

        $pengembanganDiri = [];
        foreach ($journals as $index => $j) {
            $pengembanganDiri[] = [
                'label' => sprintf('Pengembangan %02d', $index + 1),
                'date' => $j->activity_date->locale('id')->translatedFormat('l, d F Y'),
                'organizer' => $j->organizer ?: '-',
                'name' => $j->activity_name,
                'speaker' => $j->speaker ?: 'Saya sendiri',
                'description' => $j->description,
                'reflection' => $j->reflection ?: '-',
            ];
        }

        // 9. Catatan Pendamping Bruder Muda (TPPBM)
        $notes = $user->bruderNotes()->latest()->get();
        $catatanTppbm = [
            'pribadi' => $notes->where('category', 'Catatan Hidup Pribadi')->pluck('content')->all(),
            'rohani' => $notes->where('category', 'Catatan Hidup Rohani/Doa')->pluck('content')->all(),
            'komunitas' => $notes->where('category', 'Catatan Hidup Komunitas')->pluck('content')->all(),
            'kerasulan' => $notes->where('category', 'Catatan Hidup Kerasulan')->pluck('content')->all(),
            'triprasetia' => $notes->where('category', 'Catatan Hidup Triprasetia')->pluck('content')->all(),
        ];

        // 10. Penandatangan Raport (Ketua TPPBM & Administrator Data)
        $ketuaTppbm = (Auth::check() && Auth::user()->role === 'admin_ketua_tppbm')
            ? Auth::user()
            : (User::where('role', 'admin_ketua_tppbm')->where('is_active', true)->latest('id')->first()
                ?? User::where('role', 'admin_ketua_tppbm')->latest('id')->first()
                ?? User::where('username', 'galihsih')->first());

        $adminData = (Auth::check() && Auth::user()->role === 'super_admin')
            ? Auth::user()
            : (User::where('role', 'super_admin')->where('is_active', true)->first()
                ?? User::where('role', 'super_admin')->first());

        $penandatangan = [
            'ketua_tppbm' => $ketuaTppbm?->name ?? 'Br. F.A. Galih Sih Hartanta, FIC',
            'admin_data' => $adminData?->name ?? 'Br. Andrias Purwanto, FIC',
        ];

        return [
            'data_diri' => $dataDiri,
            'keluarga' => $dataKeluarga,
            'pendidikan' => $pendidikan,
            'formasi' => $formasi,
            'perutusan' => $perutusan,
            'bimbingan_bulan' => $bimbinganBulan,
            'wawanhati_bulan' => $wawanhatiBulan,
            'pengembangan_diri' => $pengembanganDiri,
            'catatan_tppbm' => $catatanTppbm,
            'penandatangan' => $penandatangan,
        ];
    }

    private function bruders()
    {
        return User::query()->where('role', 'user')->orderBy('name')->get();
    }
}
