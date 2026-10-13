<?php

namespace App\Http\Controllers;

use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
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
        
        // Jadwal mendatang
        $upcomingSchedules = $this->getUpcomingSchedules($user->id);
        
        // Sapaan provinsial terbaru
        $sapaan = \App\Models\ProvincialMessage::where('is_active', true)
            ->latest('published_at')
            ->first();

        return view('dashboard.user', compact('user', 'stats', 'upcomingSchedules', 'sapaan'));
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

    /**
     * Ambil jadwal mendatang 3 item terdekat.
     */
    private function getUpcomingSchedules(int $userId): array
    {
        $now = now();
        
        $bimbingan = SpiritualGuidanceSchedule::where('user_id', $userId)
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(2)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'bimbingan',
                    'title' => 'Bimbingan Rohani',
                    'person' => $s->mentor_name,
                    'datetime' => $s->scheduled_at,
                    'location' => $s->location,
                ];
            });

        $wawanhati = WawanhatiSchedule::where('user_id', $userId)
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(2)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'wawanhati',
                    'title' => 'Wawanhati Pilok',
                    'person' => $s->local_leader_name,
                    'datetime' => $s->scheduled_at,
                    'location' => $s->location,
                ];
            });

        $agenda = PersonalAgenda::where('user_id', $userId)
            ->where('start_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_at')
            ->limit(2)
            ->get()
            ->map(function ($a) {
                return [
                    'type' => 'lainnya',
                    'title' => $a->title,
                    'person' => $a->description,
                    'datetime' => $a->start_at,
                    'location' => $a->location,
                ];
            });

        $all = $bimbingan->merge($wawanhati)->merge($agenda)
            ->sortBy('datetime')
            ->take(3)
            ->values()
            ->toArray();

        return $all;
    }
}