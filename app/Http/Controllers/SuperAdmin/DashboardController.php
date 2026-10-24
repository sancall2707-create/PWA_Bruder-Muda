<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PersonalAgenda;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\WawanhatiSchedule;
use App\Services\SuperAdminOverviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected SuperAdminOverviewService $overviewService
    ) {}

    /**
     * Tampilkan halaman utama dashboard Super Admin.
     */
    public function index(Request $request): View
    {
        $stats = $this->overviewService->monthlySummary();
        $bruders = $this->overviewService->bruderCards();

        $now = now();
        $upBimbingan = SpiritualGuidanceSchedule::with('user')
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get()
            ->map(fn ($s) => (object) [
                'bruder'   => $s->user->name ?? 'Bruder Muda',
                'type'     => 'Bimbingan Rohani',
                'title'    => $s->mentor_name ? 'Pembimbing: ' . $s->mentor_name : 'Bimbingan Rohani',
                'datetime' => $s->scheduled_at ? $s->scheduled_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location' => $s->location ?: 'Ruang Bimbingan',
                'color'    => '#1455C0',
                'raw_date' => $s->scheduled_at,
            ]);

        $upWawanhati = WawanhatiSchedule::with('user')
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get()
            ->map(fn ($w) => (object) [
                'bruder'   => $w->user->name ?? 'Bruder Muda',
                'type'     => 'Wawanhati Pilok',
                'title'    => $w->local_leader_name ? 'Pimpinan: ' . $w->local_leader_name : 'Wawanhati Pilok',
                'datetime' => $w->scheduled_at ? $w->scheduled_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location' => $w->location ?: 'Komunitas',
                'color'    => '#00838F',
                'raw_date' => $w->scheduled_at,
            ]);

        $upPersonal = PersonalAgenda::with('user')
            ->where('start_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => (object) [
                'bruder'   => $p->user->name ?? 'Bruder Muda',
                'type'     => 'Agenda Pribadi',
                'title'    => $p->title,
                'datetime' => $p->start_at ? $p->start_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'location' => $p->location ?: 'Komunitas',
                'color'    => '#B57900',
                'raw_date' => $p->start_at,
            ]);

        $upcomingSchedules = $upBimbingan->concat($upWawanhati)->concat($upPersonal)->sortBy(fn($item) => $item->raw_date?->getTimestamp() ?? 0)->take(5)->values();

        return view('super-admin.dashboard', compact('stats', 'bruders', 'upcomingSchedules'));
    }
}
