<?php

namespace App\Services;

use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\User;
use App\Models\WawanhatiReport;
use App\Models\WawanhatiSchedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MonitoringQuery
{
    /**
     * @return array{bulan: int, bulan_akhir: int, tahun: int, bruder: int|null, period_label: string}
     */
    public function filters(Request $request): array
    {
        $now = now();
        $bulanAwal = (int) $request->input('bulan', $now->month);
        $bulanAkhir = (int) $request->input('bulan_akhir', $bulanAwal);
        $tahun = (int) $request->input('tahun', $now->year);

        if ($bulanAwal < 1 || $bulanAwal > 12) {
            $bulanAwal = (int) $now->month;
        }

        if ($bulanAkhir < 1 || $bulanAkhir > 12) {
            $bulanAkhir = $bulanAwal;
        }

        if ($bulanAwal > $bulanAkhir) {
            [$bulanAwal, $bulanAkhir] = [$bulanAkhir, $bulanAwal];
        }

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = (int) $now->year;
        }

        $bruder = $request->filled('bruder') ? (int) $request->input('bruder') : null;

        if ($bruder !== null && ! User::query()->where('role', 'user')->whereKey($bruder)->exists()) {
            $bruder = null;
        }

        $periodStart = Carbon::create($tahun, $bulanAwal, 1)->locale('id');
        $periodEnd = Carbon::create($tahun, $bulanAkhir, 1)->locale('id');

        $periodLabel = $bulanAwal === $bulanAkhir
            ? $periodStart->translatedFormat('F Y')
            : $periodStart->translatedFormat('F') . ' – ' . $periodEnd->translatedFormat('F Y');

        return [
            'bulan' => $bulanAwal,
            'bulan_akhir' => $bulanAkhir,
            'tahun' => $tahun,
            'bruder' => $bruder,
            'period_label' => $periodLabel,
        ];
    }

    /**
     * @param  array{bulan: int, tahun: int, bruder: int|null}  $filters
     */
    public function reports(array $filters): Collection
    {
        $bimbingan = $this->applyOwner(
            SpiritualGuidanceReport::query()->with(['user' => fn ($query) => $query->withTrashed()]),
            $filters,
            'meeting_at',
        )->orderByDesc('meeting_at')->get()->map(fn (SpiritualGuidanceReport $report) => $this->row(
            bruder: $this->bruderName($report->user),
            type: 'Bimbingan Rohani',
            category: 'bimbingan',
            at: $report->meeting_at,
            status: (string) $report->status,
            counterpart: (string) $report->mentor_name,
            location: $report->location,
            summary: (string) $report->report_content,
        ));

        $wawanhati = $this->applyOwner(
            WawanhatiReport::query()->with(['user' => fn ($query) => $query->withTrashed()]),
            $filters,
            'meeting_at',
        )->orderByDesc('meeting_at')->get()->map(fn (WawanhatiReport $report) => $this->row(
            bruder: $this->bruderName($report->user),
            type: 'Wawanhati Pilok',
            category: 'wawanhati',
            at: $report->meeting_at,
            status: (string) $report->status,
            counterpart: (string) $report->local_leader_name,
            location: $report->location,
            summary: (string) $report->report_content,
        ));

        $journals = $this->applyOwner(
            SelfDevelopmentJournal::query()->with(['user' => fn ($query) => $query->withTrashed()]),
            $filters,
            'activity_date',
        )->orderByDesc('activity_date')->get()->map(fn (SelfDevelopmentJournal $journal) => $this->row(
            bruder: $this->bruderName($journal->user),
            type: 'Jurnal Pengembangan Diri',
            category: 'jurnal',
            at: $journal->activity_date->copy()->startOfDay(),
            status: (string) $journal->status,
            counterpart: (string) ($journal->organizer ?: $journal->speaker ?: ''),
            location: null,
            summary: trim($journal->activity_name."\n".$journal->description),
        ));

        return collect()
            ->merge($bimbingan)
            ->merge($wawanhati)
            ->merge($journals)
            ->sortByDesc(fn (object $row) => $row->at->getTimestamp())
            ->values();
    }

    /**
     * @param  array{bulan: int, tahun: int, bruder: int|null}  $filters
     */
    public function agendas(array $filters): Collection
    {
        $bimbingan = $this->applyOwner(
            SpiritualGuidanceSchedule::query()->with(['user' => fn ($query) => $query->withTrashed()])->where('status', '!=', 'cancelled'),
            $filters,
            'scheduled_at',
        )->get()->map(function (SpiritualGuidanceSchedule $schedule) {
            $hasReport = $schedule->status === 'completed'
                || SpiritualGuidanceReport::where('user_id', $schedule->user_id)
                    ->whereDate('meeting_at', $schedule->scheduled_at->toDateString())
                    ->exists();

            $status = $hasReport ? 'completed' : $schedule->status;

            return $this->row(
                bruder: $this->bruderName($schedule->user),
                type: 'Bimbingan Rohani',
                category: 'bimbingan',
                at: $schedule->scheduled_at,
                status: (string) $status,
                counterpart: (string) $schedule->mentor_name,
                location: $schedule->location,
                summary: (string) ($schedule->topic_notes ?? ''),
            );
        });

        $wawanhati = $this->applyOwner(
            WawanhatiSchedule::query()->with(['user' => fn ($query) => $query->withTrashed()])->where('status', '!=', 'cancelled'),
            $filters,
            'scheduled_at',
        )->get()->map(function (WawanhatiSchedule $schedule) {
            $hasReport = $schedule->status === 'completed'
                || WawanhatiReport::where('user_id', $schedule->user_id)
                    ->whereDate('meeting_at', $schedule->scheduled_at->toDateString())
                    ->exists();

            $status = $hasReport ? 'completed' : $schedule->status;

            return $this->row(
                bruder: $this->bruderName($schedule->user),
                type: 'Wawanhati Pilok',
                category: 'wawanhati',
                at: $schedule->scheduled_at,
                status: (string) $status,
                counterpart: (string) $schedule->local_leader_name,
                location: $schedule->location,
                summary: (string) ($schedule->topic_notes ?? ''),
            );
        });

        $personal = $this->applyOwner(
            PersonalAgenda::query()->with(['user' => fn ($query) => $query->withTrashed()])->where('status', '!=', 'cancelled'),
            $filters,
            'start_at',
        )->get()->map(fn (PersonalAgenda $agenda) => $this->row(
            bruder: $this->bruderName($agenda->user),
            type: $agenda->title,
            category: 'lainnya',
            at: $agenda->start_at,
            status: (string) $agenda->status,
            counterpart: 'Agenda pribadi',
            location: $agenda->location,
            summary: (string) ($agenda->description ?? ''),
        ));

        return collect()
            ->merge($bimbingan)
            ->merge($wawanhati)
            ->merge($personal)
            ->sortBy(fn (object $row) => $row->at->getTimestamp())
            ->values();
    }

    /**
     * @return array{month_label: string, days: int, leading: int, selected_day: int, selected_label: string, dots: array<int, array<string, bool>>, selected_items: Collection, prev_bulan: int, prev_tahun: int, next_bulan: int, next_tahun: int}
     */
    public function calendar(Collection $items, int $year, int $month, ?string $selected): array
    {
        $cursor = Carbon::create($year, $month, 1)->locale('id')->startOfDay();
        $selectedDate = $this->selectedDay($cursor, $selected);

        $dots = [];
        foreach ($items as $item) {
            $dots[$item->at->day][$item->category] = true;
        }

        $previous = $cursor->copy()->subMonth();
        $next = $cursor->copy()->addMonth();

        return [
            'month_label' => $cursor->translatedFormat('F Y'),
            'days' => $cursor->daysInMonth,
            'leading' => $cursor->dayOfWeek,
            'selected_day' => $selectedDate->day,
            'selected_label' => $selectedDate->copy()->locale('id')->translatedFormat('l, j F Y'),
            'dots' => $dots,
            'selected_items' => $items->filter(fn (object $item) => $item->at->isSameDay($selectedDate))->values(),
            'prev_bulan' => $previous->month,
            'prev_tahun' => $previous->year,
            'next_bulan' => $next->month,
            'next_tahun' => $next->year,
        ];
    }

    private function selectedDay(Carbon $month, ?string $selected): Carbon
    {
        if ($selected) {
            try {
                $parsed = Carbon::parse($selected)->locale('id');
                if ($parsed->isSameMonth($month)) {
                    return $parsed->startOfDay();
                }
            } catch (\Throwable) {
                // Tanggal tidak valid diabaikan, lalu jatuh ke hari default.
            }
        }

        if (now()->isSameMonth($month)) {
            return now()->locale('id')->startOfDay();
        }

        return $month->copy();
    }

    private function applyOwner(Builder $query, array $filters, string $column): Builder
    {
        $bulanAwal = (int) ($filters['bulan'] ?? 1);
        $bulanAkhir = (int) ($filters['bulan_akhir'] ?? $bulanAwal);
        $tahun = (int) ($filters['tahun'] ?? now()->year);

        $start = Carbon::create($tahun, $bulanAwal, 1)->locale('id')->startOfMonth();
        $end = Carbon::create($tahun, $bulanAkhir, 1)->locale('id')->endOfMonth();

        $startVal = str_contains($column, 'date') ? $start->toDateString() : $start->toDateTimeString();
        $endVal = str_contains($column, 'date') ? $end->toDateString() : $end->toDateTimeString();

        return $query
            ->whereBetween($column, [$startVal, $endVal])
            ->when($filters['bruder'], fn ($builder) => $builder->where('user_id', $filters['bruder']));
    }

    private function bruderName(?User $user): string
    {
        return $user?->name ?: 'Bruder dihapus';
    }

    private function row(
        string $bruder,
        string $type,
        string $category,
        Carbon $at,
        string $status,
        string $counterpart,
        ?string $location,
        string $summary,
    ): object {
        $at = $at->copy()->locale('id');
        $done = in_array(strtolower($status), ['completed', 'approved', 'published', 'submitted', 'selesai', 'done'], true);
        $summary = trim(preg_replace('/\s+/', ' ', strip_tags($summary)) ?? '');

        return (object) [
            'bruder' => $bruder,
            'type' => $type,
            'category' => $category,
            'at' => $at,
            'date_label' => $at->translatedFormat('j F Y'),
            'time_label' => $at->format('H:i').' WIB',
            'status_label' => $done ? 'Selesai' : 'Menunggu',
            'status_done' => $done,
            'counterpart' => $counterpart,
            'location' => $location ?: '',
            'summary' => $summary,
            'preview' => Str::limit($summary, 160),
        ];
    }
}
