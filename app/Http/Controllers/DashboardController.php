<?php

namespace App\Http\Controllers;

use App\Models\ProvincialMessage;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use App\Services\PersonalScheduleQuery;
use App\Services\SuperAdminOverviewService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard User (Bruder Muda).
     * Dapat diakses oleh role: user, admin, super_admin.
     */
    public function user(): View
    {
        $user = auth()->user();

        // Statistik pembinaan bulan berjalan
        $stats = $this->getMonthlyStats($user->id);

        $upcomingSchedules = app(PersonalScheduleQuery::class)->upcoming($user->id);

        $sapaan = ProvincialMessage::currentSapaan();
        $announcements = ProvincialMessage::currentAnnouncements();

        $unreadMessagesCount = $user->unreadDirectMessagesCount();
        $latestUnreadMessage = $unreadMessagesCount > 0
            ? $user->directMessagesReceived()->whereNull('read_at')->latest()->first()
            : null;

        return view('dashboard.user', compact('user', 'stats', 'upcomingSchedules', 'sapaan', 'announcements', 'unreadMessagesCount', 'latestUnreadMessage'));
    }

    /**
     * Tampilkan halaman Dashboard Admin (TPPBM).
     * Dapat diakses oleh role: admin, super_admin.
     */
    public function admin(): View
    {
        $user = auth()->user();
        $overviewService = app(SuperAdminOverviewService::class);

        // 1. Ringkasan statistik bulanan
        $stats = $overviewService->monthlySummary();

        // 2. Daftar direktori kartu bruder muda
        $bruders = $overviewService->bruderCards(8);

        // 3. Feed laporan bimbingan, wawanhati, dan jurnal terbaru
        $bimbingans = SpiritualGuidanceReport::with('user')
            ->latest('meeting_at')->take(4)->get()
            ->map(fn ($r) => (object) [
                'user'          => $r->user,
                'date'          => $r->meeting_at,
                'date_label'    => $r->meeting_at ? $r->meeting_at->translatedFormat('d M Y') : '-',
                'type_label'    => 'Bimbingan Rohani',
                'counterpart'   => 'Pembimbing: ' . ($r->mentor_name ?: '-'),
                'preview'       => $r->report_content,
                'status_label'  => 'Terkirim',
                'theme_color'   => '#1455C0',
                'bg_color'      => '#EFF8FF',
                'border_color'  => '#B2DDFF',
            ]);

        $wawanhatis = WawanhatiReport::with('user')
            ->latest('meeting_at')->take(4)->get()
            ->map(fn ($r) => (object) [
                'user'          => $r->user,
                'date'          => $r->meeting_at,
                'date_label'    => $r->meeting_at ? $r->meeting_at->translatedFormat('d M Y') : '-',
                'type_label'    => 'Wawanhati Pilok',
                'counterpart'   => 'Pimpinan: ' . ($r->local_leader_name ?: '-'),
                'preview'       => $r->report_content,
                'status_label'  => 'Terkirim',
                'theme_color'   => '#027A48',
                'bg_color'      => '#ECFDF3',
                'border_color'  => '#ABEFC6',
            ]);

        $journals = SelfDevelopmentJournal::with('user')
            ->latest('activity_date')->take(4)->get()
            ->map(fn ($r) => (object) [
                'user'          => $r->user,
                'date'          => $r->activity_date,
                'date_label'    => $r->activity_date ? $r->activity_date->translatedFormat('d M Y') : '-',
                'type_label'    => 'Jurnal Pengembangan Diri',
                'counterpart'   => 'Kegiatan: ' . ($r->activity_name ?: '-'),
                'preview'       => $r->reflection ?: $r->description,
                'status_label'  => 'Dipublikasi',
                'theme_color'   => '#B57900',
                'bg_color'      => '#FFF8E1',
                'border_color'  => '#FFD966',
            ]);

        $recentFeed = $bimbingans->concat($wawanhatis)->concat($journals)
            ->sortByDesc(fn ($item) => $item->date)
            ->take(5)
            ->values();

        // 4. Jadwal pembinaan terdekat
        $now = now();
        $upBimbingan = SpiritualGuidanceSchedule::with('user')
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get()
            ->map(fn ($s) => (object) [
                'bruder'   => $s->user->name ?? 'Bruder Muda',
                'type'     => 'Bimbingan Rohani',
                'title'    => $s->mentor_name ? 'Pembimbing: ' . $s->mentor_name : 'Bimbingan Rohani',
                'datetime' => $s->scheduled_at ? $s->scheduled_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location' => $s->location ?: 'Ruang Bimbingan',
                'color'    => '#1455C0',
            ]);

        $upWawanhati = WawanhatiSchedule::with('user')
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get()
            ->map(fn ($w) => (object) [
                'bruder'   => $w->user->name ?? 'Bruder Muda',
                'type'     => 'Wawanhati Pilok',
                'title'    => $w->local_leader_name ? 'Pimpinan: ' . $w->local_leader_name : 'Wawanhati Pilok',
                'datetime' => $w->scheduled_at ? $w->scheduled_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location' => $w->location ?: 'Komunitas',
                'color'    => '#027A48',
            ]);

        $upcomingSchedules = $upBimbingan->concat($upWawanhati)->sortBy('datetime')->take(3)->values();

        // 5. Sapaan & pengumuman aktif
        $sapaan = ProvincialMessage::currentSapaan();
        $announcements = ProvincialMessage::currentAnnouncements();

        return view('dashboard.admin', compact(
            'user',
            'stats',
            'bruders',
            'recentFeed',
            'upcomingSchedules',
            'sapaan',
            'announcements'
        ));
    }

    /**
     * Tampilkan halaman Dashboard Super Admin.
     * Dapat diakses oleh role: super_admin.
     */
    public function superAdmin(): View
    {
        return view('dashboard.super-admin');
    }

    /**
     * Hitung statistik pembinaan bulanan dan tahunan user.
     * Aturan: Pembinaan maksimal 2 kali per bulan (Bimbingan Rohani & Wawanhati),
     * setahun = 2 x 12 = 24 kali.
     */
    private function getMonthlyStats(int $userId): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        $counted = ['completed', 'approved', 'published', 'submitted'];

        $bimbinganBulan = SpiritualGuidanceReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->count();

        $wawanhatiBulan = WawanhatiReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->count();

        $jurnalBulan = SelfDevelopmentJournal::where('user_id', $userId)
            ->whereBetween('activity_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', $counted)
            ->count();

        $bimbinganTahun = SpiritualGuidanceReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$yearStart, $yearEnd])
            ->whereIn('status', $counted)
            ->count();

        $wawanhatiTahun = WawanhatiReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$yearStart, $yearEnd])
            ->whereIn('status', $counted)
            ->count();

        $jurnalTahun = SelfDevelopmentJournal::where('user_id', $userId)
            ->whereBetween('activity_date', [$yearStart->toDateString(), $yearEnd->toDateString()])
            ->whereIn('status', $counted)
            ->count();

        // Pembinaan = Bimbingan + Wawanhati (Maksimal 2x per bulan)
        $pembinaanBulan = $bimbinganBulan + $wawanhatiBulan;
        $targetBulan = 2; // Maksimal 2 kali dalam sebulan
        $persentase = min(100, (int) round(($pembinaanBulan / $targetBulan) * 100));

        // Pembinaan Tahunan = 2 x 12 = 24 dalam satu tahun
        $pembinaanTahun = $bimbinganTahun + $wawanhatiTahun;
        $targetTahun = 24;
        $persentaseTahun = min(100, (int) round(($pembinaanTahun / $targetTahun) * 100));

        $latestBimbingan = SpiritualGuidanceReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->latest('meeting_at')
            ->first();

        $latestWawanhati = WawanhatiReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->latest('meeting_at')
            ->first();

        $latestJurnal = SelfDevelopmentJournal::where('user_id', $userId)
            ->whereBetween('activity_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', $counted)
            ->latest('activity_date')
            ->first();

        return [
            'bimbingan' => $bimbinganBulan,
            'wawanhati' => $wawanhatiBulan,
            'jurnal' => $jurnalBulan,
            'total' => $pembinaanBulan,
            'target' => $targetBulan,
            'persentase' => $persentase,
            'pembinaan_bulan' => $pembinaanBulan,
            'target_bulan' => $targetBulan,
            'pembinaan_tahun' => $pembinaanTahun,
            'target_tahun' => $targetTahun,
            'persentase_tahun' => $persentaseTahun,
            'jurnal_tahun' => $jurnalTahun,
            'checklist' => [
                'bimbingan' => [
                    'done' => $bimbinganBulan > 0,
                    'count' => $bimbinganBulan,
                    'item' => $latestBimbingan,
                ],
                'wawanhati' => [
                    'done' => $wawanhatiBulan > 0,
                    'count' => $wawanhatiBulan,
                    'item' => $latestWawanhati,
                ],
                'jurnal' => [
                    'done' => $jurnalBulan > 0,
                    'count' => $jurnalBulan,
                    'item' => $latestJurnal,
                ],
            ],
        ];
    }
}
