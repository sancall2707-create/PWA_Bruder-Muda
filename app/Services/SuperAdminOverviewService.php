<?php

namespace App\Services;

use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use Illuminate\Support\Carbon;

class SuperAdminOverviewService
{
    /**
     * Ringkasan dashboard admin untuk bulan berjalan dan akumulasi tahunan.
     *
     * Bulan berjalan memakai rumus yang sudah ada: jumlah sesi wajib
     * (bimbingan rohani + wawanhati) bruder aktif dibanding target 2 × bruder aktif.
     * Akumulasi tahunan memakai aturan yang sama dengan kartu bruder, diagregasi:
     * sesi wajib sejak 1 Januari sampai saat ini (Asia/Jakarta) dibanding target
     * 24 × bruder aktif. Jurnal tidak masuk target. Persentase dibatasi 100.
     *
     * @return array<string, mixed>
     */
    public function monthlySummary(?Carbon $month = null): array
    {
        $month = $month ? $month->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $startOfYear = $month->copy()->startOfYear();
        $counted = ['submitted', 'published', 'completed', 'approved'];

        $activeIds = User::where('role', 'user')->where('is_active', true)->pluck('id');
        $activeBruders = $activeIds->count();
        $inactiveBruders = User::where('role', 'user')->where('is_active', false)->count();
        $admins = User::whereIn('role', ['admin', 'admin_ketua_tppbm', 'super_admin'])->count();

        $bimbinganSubmitted = SpiritualGuidanceReport::whereIn('user_id', $activeIds)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->count();

        $wawanhatiSubmitted = WawanhatiReport::whereIn('user_id', $activeIds)
            ->whereBetween('meeting_at', [$start, $end])
            ->whereIn('status', $counted)
            ->count();

        $journalPublished = SelfDevelopmentJournal::whereIn('user_id', $activeIds)
            ->whereBetween('activity_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['published', 'submitted', 'completed', 'approved'])
            ->count();

        $expectedReports = $activeBruders * 2;
        $completedReports = $bimbinganSubmitted + $wawanhatiSubmitted;
        $completionPercent = $expectedReports === 0
            ? 0
            : min(100, (int) round(($completedReports / $expectedReports) * 100));

        $bimbinganTahun = SpiritualGuidanceReport::whereIn('user_id', $activeIds)
            ->whereBetween('meeting_at', [$startOfYear, $month])
            ->whereIn('status', $counted)
            ->count();

        $wawanhatiTahun = WawanhatiReport::whereIn('user_id', $activeIds)
            ->whereBetween('meeting_at', [$startOfYear, $month])
            ->whereIn('status', $counted)
            ->count();

        $pembinaanTahun = $bimbinganTahun + $wawanhatiTahun;
        $targetTahun = $activeBruders * 24;
        $persentaseTahun = $targetTahun === 0
            ? 0
            : min(100, (int) round(($pembinaanTahun / $targetTahun) * 100));

        $namaBulan = $month->copy()->locale('id')->translatedFormat('F');

        return [
            'period_label' => $month->translatedFormat('F Y'),
            'active_bruders' => $activeBruders,
            'inactive_bruders' => $inactiveBruders,
            'admins' => $admins,
            'expected_reports' => $expectedReports,
            'completed_reports' => $completedReports,
            'completion_percent' => $completionPercent,
            'bimbingan_submitted' => $bimbinganSubmitted,
            'wawanhati_submitted' => $wawanhatiSubmitted,
            'journal_published' => $journalPublished,
            'upcoming_bimbingan' => SpiritualGuidanceSchedule::where('scheduled_at', '>=', now())->count(),
            'upcoming_wawanhati' => WawanhatiSchedule::where('scheduled_at', '>=', now())->count(),
            'upcoming_agendas' => PersonalAgenda::where('start_at', '>=', now())->count(),
            'pembinaan_bulan' => $completedReports,
            'target_bulan' => $expectedReports,
            'persentase_bulan' => $completionPercent,
            'pembinaan_tahun' => $pembinaanTahun,
            'target_tahun' => $targetTahun,
            'persentase_tahun' => $persentaseTahun,
            'label_tahunan' => "Januari – {$namaBulan} {$month->year}",
            'label_bulan' => "Bulan {$namaBulan}",
        ];
    }

    /**
     * Daftar bruder muda dan admin beserta capaian laporan bulan berjalan.
     */
    public function bruderDirectory(?string $search = null, bool $includeAdmins = true)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $counted = ['submitted', 'published', 'completed', 'approved'];

        $query = User::query();
        if (! $includeAdmins) {
            $query->where('role', 'user');
        }

        $search = trim((string) $search);
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($inner) use ($like) {
                $inner->where('name', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('community_location', 'like', $like);
            });
        }

        return $query->withCount([
            'spiritualGuidanceReports as bimbingan_month_count' => fn ($reports) => $reports
                ->whereBetween('meeting_at', [$start, $end])
                ->whereIn('status', $counted),
            'wawanhatiReports as wawanhati_month_count' => fn ($reports) => $reports
                ->whereBetween('meeting_at', [$start, $end])
                ->whereIn('status', $counted),
            'selfDevelopmentJournals as journal_month_count' => fn ($journals) => $journals
                ->whereBetween('activity_date', [$start->toDateString(), $end->toDateString()])
                ->whereIn('status', $counted),
        ])->orderByDesc('is_active')->orderBy('name');
    }

    /**
     * Daftar bruder muda untuk kartu ringkas dashboard.
     */
    public function bruderCards(int $limit = 8)
    {
        return $this->bruderDirectory(includeAdmins: false)->limit($limit)->get();
    }
}
