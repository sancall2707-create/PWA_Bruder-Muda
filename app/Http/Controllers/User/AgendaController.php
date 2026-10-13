<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PersonalAgenda;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\WawanhatiSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Tampilkan halaman Agenda Pribadi Bruder Muda secara kronologis.
     */
    public function index(): View
    {
        $userId = auth()->id();
        $now = now();

        $bimbinganSchedules = SpiritualGuidanceSchedule::where('user_id', $userId)
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'bimbingan',
                    'id' => $s->id,
                    'category' => 'bimbingan',
                    'title' => 'Bimbingan Rohani',
                    'target_person' => $s->mentor_name,
                    'date_formatted' => $s->scheduled_at->translatedFormat('d M Y'),
                    'time_formatted' => $s->scheduled_at->format('H:i'),
                    'location' => $s->location,
                    'notes' => $s->topic_notes,
                    'status' => $s->status,
                ];
            });

        $wawanhatiSchedules = WawanhatiSchedule::where('user_id', $userId)
            ->where('scheduled_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'wawanhati',
                    'id' => $s->id,
                    'category' => 'wawanhati',
                    'title' => 'Wawanhati Pilok',
                    'target_person' => $s->local_leader_name,
                    'date_formatted' => $s->scheduled_at->translatedFormat('d M Y'),
                    'time_formatted' => $s->scheduled_at->format('H:i'),
                    'location' => $s->location,
                    'notes' => $s->topic_notes,
                    'status' => $s->status,
                ];
            });

        $personalAgendas = PersonalAgenda::where('user_id', $userId)
            ->where('start_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_at')
            ->get()
            ->map(function ($a) {
                return [
                    'type' => 'lainnya',
                    'id' => $a->id,
                    'category' => 'lainnya',
                    'title' => $a->title,
                    'target_person' => $a->description,
                    'date_formatted' => $a->start_at->translatedFormat('d M Y'),
                    'time_formatted' => $a->start_at->format('H:i'),
                    'location' => $a->location,
                    'notes' => $a->description,
                    'status' => $a->status,
                ];
            });

        $allAgendas = $bimbinganSchedules
            ->merge($wawanhatiSchedules)
            ->merge($personalAgendas)
            ->sortBy(function ($a) {
                return $a['date_formatted'] . ' ' . $a['time_formatted'];
            })
            ->values();

        $agendas = $allAgendas->map(fn($a) => (object) $a);

        return view('user.agenda.index', compact('agendas', 'allAgendas'));
    }

    /**
     * Formulir tambah Jadwal Bimbingan Rohani.
     */
    public function createBimbingan(): View
    {
        return view('user.agenda.create-bimbingan');
    }

    /**
     * Formulir tambah Jadwal Wawanhati Pimpinan Lokal.
     */
    public function createWawanhati(): View
    {
        return view('user.agenda.create-wawanhati');
    }

    /**
     * Formulir tambah Agenda Lainnya.
     */
    public function createLainnya(): View
    {
        return view('user.agenda.create-lainnya');
    }

    /**
     * Simpan jadwal / agenda baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('agenda_type', 'lainnya');

        if ($type === 'bimbingan') {
            $validated = $request->validate([
                'mentor_name' => ['required', 'string', 'max:255'],
                'meeting_date' => ['required', 'date'],
                'meeting_time' => ['nullable', 'string'],
                'location' => ['nullable', 'string', 'max:255'],
                'topic_notes' => ['nullable', 'string'],
            ]);

            $datetime = $validated['meeting_date'] . ' ' . ($validated['meeting_time'] ?? '10:00') . ':00';

            SpiritualGuidanceSchedule::create([
                'user_id' => auth()->id(),
                'mentor_name' => $validated['mentor_name'],
                'scheduled_at' => $datetime,
                'location' => $validated['location'] ?? null,
                'topic_notes' => $validated['topic_notes'] ?? null,
                'status' => 'scheduled',
            ]);
        } elseif ($type === 'wawanhati') {
            $validated = $request->validate([
                'local_leader_name' => ['required', 'string', 'max:255'],
                'meeting_date' => ['required', 'date'],
                'meeting_time' => ['nullable', 'string'],
                'location' => ['nullable', 'string', 'max:255'],
                'topic_notes' => ['nullable', 'string'],
            ]);

            $datetime = $validated['meeting_date'] . ' ' . ($validated['meeting_time'] ?? '14:00') . ':00';

            WawanhatiSchedule::create([
                'user_id' => auth()->id(),
                'local_leader_name' => $validated['local_leader_name'],
                'scheduled_at' => $datetime,
                'location' => $validated['location'] ?? null,
                'topic_notes' => $validated['topic_notes'] ?? null,
                'status' => 'scheduled',
            ]);
        } else {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'start_date' => ['required', 'date'],
                'start_time' => ['nullable', 'string'],
                'location' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            $datetime = $validated['start_date'] . ' ' . ($validated['start_time'] ?? '09:00') . ':00';

            PersonalAgenda::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'start_at' => $datetime,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'scheduled',
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil ditambahkan ke jadwal Anda.');
    }
}