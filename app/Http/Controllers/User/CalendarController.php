<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PersonalScheduleQuery;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Tampilkan kalender kegiatan Bruder Muda dari jadwal yang tersimpan.
     */
    public function index(Request $request): View
    {
        $month = $this->resolveMonth($request->query('month'));
        $selectedDate = $this->resolveSelectedDate($request->query('date'), $month);
        $monthItems = app(PersonalScheduleQuery::class)->inMonth(auth()->id(), $month);

        $dots = [
            'bimbingan' => [],
            'wawanhati' => [],
            'lainnya' => [],
        ];

        foreach ($monthItems as $item) {
            $dots[$item->category][$item->starts_at->day] = true;
        }

        $selectedDateAgendas = $monthItems
            ->filter(fn (object $item) => $item->starts_at->isSameDay($selectedDate))
            ->values();

        return view('user.kalender.index', [
            'month' => $month,
            'monthLabel' => $month->copy()->locale('id')->translatedFormat('F Y'),
            'monthKey' => $month->format('Y-m'),
            'prevMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
            'leadingBlanks' => $month->dayOfWeek,
            'daysInMonth' => $month->daysInMonth,
            'dots' => $dots,
            'selectedDate' => $selectedDate,
            'selectedLabel' => $selectedDate->copy()->locale('id')->translatedFormat('l, d F Y'),
            'selectedDateAgendas' => $selectedDateAgendas,
        ]);
    }

    private function resolveMonth(mixed $month): Carbon
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            try {
                return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            } catch (\Throwable) {
                // Lanjut ke bulan berjalan.
            }
        }

        return now()->startOfMonth();
    }

    private function resolveSelectedDate(mixed $date, Carbon $month): Carbon
    {
        if (is_string($date)) {
            try {
                $parsed = Carbon::parse($date)->startOfDay();
                if ($parsed->isSameMonth($month)) {
                    return $parsed;
                }
            } catch (\Throwable) {
                // Lanjut ke tanggal bawaan.
            }
        }

        if (now()->isSameMonth($month)) {
            return now()->startOfDay();
        }

        return $month->copy()->startOfDay();
    }
}
