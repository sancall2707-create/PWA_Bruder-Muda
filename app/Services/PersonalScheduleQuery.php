<?php

namespace App\Services;

use App\Models\PersonalAgenda;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\WawanhatiSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PersonalScheduleQuery
{
    /**
     * Semua jadwal milik satu bruder, urut dari yang paling awal.
     */
    public function forUser(int $userId): Collection
    {
        $bimbingan = SpiritualGuidanceSchedule::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(fn (SpiritualGuidanceSchedule $schedule) => $this->item(
                id: $schedule->id,
                category: 'bimbingan',
                title: 'Bimbingan Rohani',
                person: $schedule->mentor_name,
                startsAt: $schedule->scheduled_at,
                location: $schedule->location,
                notes: $schedule->topic_notes,
                status: $schedule->status,
            ));

        $wawanhati = WawanhatiSchedule::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(fn (WawanhatiSchedule $schedule) => $this->item(
                id: $schedule->id,
                category: 'wawanhati',
                title: 'Wawanhati Pimpinan Lokal',
                person: $schedule->local_leader_name,
                startsAt: $schedule->scheduled_at,
                location: $schedule->location,
                notes: $schedule->topic_notes,
                status: $schedule->status,
            ));

        $personal = PersonalAgenda::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(fn (PersonalAgenda $agenda) => $this->item(
                id: $agenda->id,
                category: 'lainnya',
                title: $agenda->title,
                person: 'Agenda pribadi',
                startsAt: $agenda->start_at,
                location: $agenda->location,
                notes: $agenda->description,
                status: $agenda->status,
            ));

        return collect()
            ->concat($bimbingan)
            ->concat($wawanhati)
            ->concat($personal)
            ->sortBy(fn (object $item) => $item->starts_at->getTimestamp())
            ->values();
    }

    public function inMonth(int $userId, Carbon $month): Collection
    {
        return $this->forUser($userId)
            ->filter(fn (object $item) => $item->starts_at->isSameMonth($month))
            ->values();
    }

    public function upcoming(int $userId, int $days = 7, int $limit = 3): Collection
    {
        $until = now()->copy()->addDays($days);

        return $this->forUser($userId)
            ->filter(fn (object $item) => $item->starts_at->between(now(), $until))
            ->take($limit)
            ->values();
    }

    private function item(
        int $id,
        string $category,
        string $title,
        string $person,
        Carbon $startsAt,
        ?string $location,
        ?string $notes,
        string $status,
    ): object {
        $startsAt = $startsAt->copy()->locale('id');

        return (object) [
            'id' => $id,
            'category' => $category,
            'title' => $title,
            'target_person' => $person,
            'person' => $person,
            'starts_at' => $startsAt,
            'date_formatted' => $startsAt->translatedFormat('j F Y'),
            'time_formatted' => $startsAt->format('H:i').' WIB',
            'location' => $location,
            'notes' => $notes,
            'status' => $status,
        ];
    }
}
