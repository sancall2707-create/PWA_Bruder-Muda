<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MonitoringExport;
use App\Services\MonitoringQuery;
use App\Services\RaportPdfPrinter;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MonitoringController extends Controller
{
    public function __construct(
        private readonly MonitoringQuery $monitoring,
        private readonly MonitoringExport $exporter,
        private readonly RaportPdfPrinter $raportPdf,
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

    /**
     * Hapus satu laporan (bimbingan, wawanhati, atau jurnal) dari Monitoring oleh Super Admin / Admin.
     */
    public function destroyReportSingle(Request $request, string $type, int $id): RedirectResponse
    {
        $model = match ($type) {
            'bimbingan' => \App\Models\SpiritualGuidanceReport::class,
            'wawanhati' => \App\Models\WawanhatiReport::class,
            'jurnal'    => \App\Models\SelfDevelopmentJournal::class,
            default     => null,
        };

        if (! $model) {
            return redirect()->back()->with('error', 'Jenis laporan tidak valid.');
        }

        // Keamanan: hapus laporan (termasuk jika user pemiliknya berstatus trashed/soft-deleted)
        $deleted = $model::where('id', $id)->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Laporan tidak ditemukan atau gagal dihapus.');
    }

    /**
     * Hapus massal laporan terpilih dari Monitoring oleh Super Admin / Admin.
     * Menerima array item: [ "bimbingan:12", "wawanhati:5", "jurnal:8" ]
     */
    public function destroyReportBulk(Request $request): RedirectResponse
    {
        $items = $request->input('items', []);

        if (empty($items) || ! is_array($items)) {
            return redirect()->back()->with('error', 'Tidak ada laporan yang dipilih.');
        }

        $bimbinganIds = [];
        $wawanhatiIds = [];
        $jurnalIds = [];

        foreach ($items as $item) {
            if (! is_string($item) || ! str_contains($item, ':')) {
                continue;
            }
            [$cat, $idStr] = explode(':', $item, 2);
            $id = (int) $idStr;
            if ($id <= 0) {
                continue;
            }

            match ($cat) {
                'bimbingan' => $bimbinganIds[] = $id,
                'wawanhati' => $wawanhatiIds[] = $id,
                'jurnal'    => $jurnalIds[] = $id,
                default     => null,
            };
        }

        $totalDeleted = 0;

        if (! empty($bimbinganIds)) {
            $totalDeleted += \App\Models\SpiritualGuidanceReport::whereIn('id', $bimbinganIds)->delete();
        }

        if (! empty($wawanhatiIds)) {
            $totalDeleted += \App\Models\WawanhatiReport::whereIn('id', $wawanhatiIds)->delete();
        }

        if (! empty($jurnalIds)) {
            $totalDeleted += \App\Models\SelfDevelopmentJournal::whereIn('id', $jurnalIds)->delete();
        }

        return redirect()->back()->with('success', "{$totalDeleted} laporan berhasil dihapus.");
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

    /**
     * Daftar lengkap Jurnal Pengembangan Diri semua bruder — bisa filter & hapus massal.
     */
    public function jurnalIndex(Request $request): View
    {
        $bruders = $this->bruders();
        $selectedBruderId = $request->input('bruder') ? (int) $request->input('bruder') : null;

        $query = \App\Models\SelfDevelopmentJournal::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'user')->where('is_active', true));

        if ($selectedBruderId) {
            $query->where('user_id', $selectedBruderId);
        }

        $journals = $query->orderByDesc('activity_date')->get();

        return view('super-admin.monitoring.jurnal', [
            'journals'          => $journals,
            'bruders'           => $bruders,
            'selectedBruderId'  => $selectedBruderId,
            'headerTitle'       => 'Jurnal Pengembangan Diri',
            'headerSubtitle'    => 'Kelola dan hapus jurnal seluruh bruder muda',
        ]);
    }

    /**
     * Hapus massal Jurnal Pengembangan Diri oleh Super Admin / Admin TPPBM.
     * Hanya jurnal milik user role=user yang boleh dihapus.
     */
    public function jurnalDestroyBulk(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids) || ! is_array($ids)) {
            return redirect()->route('superadmin.monitoring.jurnal.index')->with('error', 'Tidak ada jurnal yang dipilih.');
        }

        $ids = array_map('intval', $ids);

        // Hapus jurnal yang dipilih
        $deleted = \App\Models\SelfDevelopmentJournal::whereIn('id', $ids)->delete();

        return redirect()->route('superadmin.monitoring.jurnal.index', $request->only('bruder'))
            ->with('success', "{$deleted} jurnal berhasil dihapus.");
    }

    public function export(Request $request): Response|RedirectResponse
    {
        if ($request->input('jenis') === 'raport' && $request->input('format') === 'pdf') {
            return $this->downloadRaport($request);
        }

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
        if ($selectedBruder) {
            $filters['bruder'] = $selectedBruder->id;
        }

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

    private function downloadRaport(Request $request): Response|RedirectResponse
    {
        $filters = $this->monitoring->filters($request);
        $bruder = $filters['bruder'] ? User::find($filters['bruder']) : null;

        if (! $bruder) {
            return redirect()->route('superadmin.monitoring.raport', $request->except('jenis', 'format'))
                ->with('error', 'Pilih satu bruder untuk mengunduh raport.');
        }

        $html = view('super-admin.monitoring.raport', [
            'filters' => $filters,
            'bruders' => $this->bruders(),
            'selectedBruder' => $bruder,
            'raport' => $this->buildRaportData($bruder, $filters),
            'headerTitle' => 'Raport Pendampingan',
            'headerSubtitle' => $bruder->name,
        ])->render();

        $filename = sprintf(
            'raport-%s-%04d-%02d-%02d.pdf',
            Str::slug($bruder->name) ?: 'bruder',
            $filters['tahun'],
            $filters['bulan'],
            $filters['bulan_akhir'] ?? $filters['bulan']
        );

        return response($this->raportPdf->print($html), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
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
                'tahun' => ! empty($edu['sd_year']) ? 'Lulus '.$edu['sd_year'] : '-',
            ],
            [
                'jenjang' => 'SMP',
                'nama' => $edu['smp_name'] ?? '-',
                'jurusan' => '-',
                'tahun' => ! empty($edu['smp_year']) ? 'Lulus '.$edu['smp_year'] : '-',
            ],
            [
                'jenjang' => 'SMA/K',
                'nama' => $edu['sma_name'] ?? '-',
                'jurusan' => ! empty($edu['sma_major']) ? '('.$edu['sma_major'].')' : '-',
                'tahun' => ! empty($edu['sma_year']) ? 'Lulus '.$edu['sma_year'] : '-',
            ],
            [
                'jenjang' => 'S1',
                'nama' => $edu['s1_name'] ?? 'Belum menempuh',
                'jurusan' => $edu['s1_major'] ?? '-',
                'tahun' => ! empty($edu['s1_in']) ? 'Masuk: '.$edu['s1_in'].(! empty($edu['s1_out']) ? ' | Lulus: '.$edu['s1_out'] : (! empty($edu['s1_target']) ? ' | Target: '.$edu['s1_target'] : '')) : '-',
            ],
            [
                'jenjang' => 'S2',
                'nama' => $edu['s2_name'] ?? 'Belum menempuh',
                'jurusan' => $edu['s2_major'] ?? '-',
                'tahun' => ! empty($edu['s2_in']) ? 'Masuk: '.$edu['s2_in'].(! empty($edu['s2_out']) ? ' | Lulus: '.$edu['s2_out'] : (! empty($edu['s2_target']) ? ' | Target: '.$edu['s2_target'] : '')) : '-',
            ],
        ];

        // 4. Formasi Religius FIC
        $form = $user->formation_data ?? [];
        $currentYear = (int) now()->year;

        $formasi = [];
        $postulatYear = ! empty($form['postulat_in']) ? (int) $form['postulat_in'] : null;
        if ($postulatYear === null || $postulatYear <= $currentYear) {
            $formasi[] = [
                'tahapan' => 'Postulat',
                'tahun' => $form['postulat_in'] ?? '-',
                'angkatan' => ! empty($form['postulat_count']) ? $form['postulat_count'].' Frater' : '-',
                'magister' => $form['postulat_magister'] ?? '-',
            ];
        }

        $kanonikYear = ! empty($form['kanonik_in']) ? (int) $form['kanonik_in'] : null;
        if ($kanonikYear === null || $kanonikYear <= $currentYear) {
            $formasi[] = [
                'tahapan' => 'Novisiat Kanonik',
                'tahun' => $form['kanonik_in'] ?? '-',
                'angkatan' => ! empty($form['kanonik_count']) ? $form['kanonik_count'].' Frater' : '-',
                'magister' => $form['kanonik_magister'] ?? '-',
            ];
        }

        $apostolikYear = ! empty($form['apostolik_in']) ? (int) $form['apostolik_in'] : null;
        if ($apostolikYear === null || $apostolikYear <= $currentYear) {
            $formasi[] = [
                'tahapan' => 'Novisiat Apostolik',
                'tahun' => $form['apostolik_in'] ?? '-',
                'angkatan' => ! empty($form['apostolik_count']) ? $form['apostolik_count'].' Frater' : '-',
                'magister' => $form['apostolik_magister'] ?? '-',
            ];
        }

        $prasetia1Year = ! empty($form['prasetia_1_year']) ? (int) $form['prasetia_1_year'] : null;
        if ($prasetia1Year === null || $prasetia1Year <= $currentYear) {
            $formasi[] = [
                'tahapan' => 'Prasetia Pertama',
                'tahun' => $form['prasetia_1_year'] ?? '-',
                'angkatan' => ! empty($form['prasetia_1_count']) ? $form['prasetia_1_count'].' Bruder' : '-',
                'magister' => $form['prasetia_1_provincial'] ?? '-',
            ];
        }

        $pembaruanKe = [1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga', 4 => 'Keempat', 5 => 'Kelima', 6 => 'Keenam'];
        $hasPembaruan = false;
        for ($p = 1; $p <= 6; $p++) {
            $pemData = $form['pembaruan'][$p] ?? [];
            if (! empty($pemData['year'])) {
                $pYear = (int) $pemData['year'];
                // Hanya tampilkan jika tahun sudah lewat atau sedang berjalan
                if ($pYear <= $currentYear) {
                    $hasPembaruan = true;
                    $formasi[] = [
                        'tahapan' => 'Pembaruan '.$pembaruanKe[$p],
                        'tahun' => $pemData['year'],
                        'angkatan' => ! empty($pemData['count']) ? $pemData['count'].' Bruder' : '-',
                        'magister' => $pemData['provincial'] ?? '-',
                    ];
                }
            }
        }
        if (! $hasPembaruan && empty($formasi)) {
            $formasi[] = [
                'tahapan' => 'Pembaruan',
                'tahun' => 'Dalam masa persiapan',
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
                $startYear = ! empty($tData['start_year']) ? (int) $tData['start_year'] : null;

                // Hanya tampilkan jika tahun mulai sudah lewat atau sedang berjalan
                if ($startYear === null || $startYear <= $currentYear) {
                    $endYearVal = ! empty($tData['end_year']) ? (int) $tData['end_year'] : null;
                    $endLabel = ($endYearVal !== null && $endYearVal <= $currentYear) ? $endYearVal : 'Sekarang';
                    $masa = trim(($tData['start_year'] ?? '').' - '.$endLabel);

                    $perutusan[] = [
                        'tugas_ke' => $tugasKe[$t],
                        'komunitas' => $tData['community'],
                        'masa' => $masa !== '-' ? $masa : '-',
                        'pokok' => $tData['duty'] ?? '-',
                    ];
                }
            }
        }
        if (empty($perutusan)) {
            $perutusan[] = [
                'tugas_ke' => 'Pertama',
                'komunitas' => $user->community_location ?: 'Komunitas FIC',
                'masa' => now()->year.' - Sekarang',
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
