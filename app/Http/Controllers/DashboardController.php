<?php

namespace App\Http\Controllers;

use App\Models\ProvincialMessage;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\WawanhatiReport;
use App\Services\PersonalScheduleQuery;
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

        return view('dashboard.user', compact('user', 'stats', 'upcomingSchedules', 'sapaan', 'announcements'));
    }

    /**
     * Tampilkan halaman Dashboard Admin.
     * Dapat diakses oleh role: admin, super_admin.
     */
    public function admin(): View
    {
        return view('dashboard.admin');
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
     * Hitung statistik pembinaan bulanan user.
     */
    private function getMonthlyStats(int $userId): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $bimbinganSelesai = SpiritualGuidanceReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', ['completed', 'approved', 'published', 'submitted'])
            ->count();

        $wawanhatiSelesai = WawanhatiReport::where('user_id', $userId)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', ['completed', 'approved', 'published', 'submitted'])
            ->count();

        $jurnalSelesai = SelfDevelopmentJournal::where('user_id', $userId)
            ->whereBetween('activity_date', [$start, $end])
            ->whereIn('status', ['completed', 'approved', 'published', 'submitted'])
            ->count();

        $totalSelesai = $bimbinganSelesai + $wawanhatiSelesai + $jurnalSelesai;
        $target = 3; // minimal 3 laporan per bulan
        $persentase = min(100, round(($totalSelesai / $target) * 100));

        return [
            'bimbingan' => $bimbinganSelesai,
            'wawanhati' => $wawanhatiSelesai,
            'jurnal' => $jurnalSelesai,
            'total' => $totalSelesai,
            'target' => $target,
            'persentase' => $persentase,
        ];
    }
}
