<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PersonalAgenda;
use App\Models\SpiritualGuidanceSchedule;
use App\Models\WawanhatiSchedule;
use App\Services\PersonalScheduleQuery;
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
        $month = now();
        $agendas = app(PersonalScheduleQuery::class)->inMonth(auth()->id(), $month);
        $monthLabel = $month->locale('id')->translatedFormat('F Y');

        return view('user.agenda.index', compact('agendas', 'monthLabel'));
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
        $type = $request->validate([
            'agenda_type' => ['required', 'in:bimbingan,wawanhati,lainnya'],
        ], [
            'agenda_type.required' => 'Jenis agenda wajib dipilih.',
            'agenda_type.in' => 'Jenis agenda tidak dikenali.',
        ])['agenda_type'];

        if ($type === 'bimbingan') {
            $validated = $request->validate([
                'mentor_name' => ['required', 'string', 'max:255'],
                'meeting_date' => ['required', 'date'],
                'meeting_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'location' => ['required', 'string', 'max:255'],
                'topic_notes' => ['nullable', 'string', 'max:2000'],
            ], [
                'mentor_name.required' => 'Nama pembimbing rohani wajib diisi.',
                'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
                'meeting_time.required' => 'Waktu pertemuan wajib diisi.',
                'meeting_time.regex' => 'Format waktu pertemuan tidak valid.',
                'location.required' => 'Tempat / ruangan wajib diisi.',
            ]);

            $datetime = $this->combineDateAndTime($validated['meeting_date'], $validated['meeting_time']);

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
                'meeting_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'location' => ['required', 'string', 'max:255'],
                'topic_notes' => ['nullable', 'string', 'max:2000'],
            ], [
                'local_leader_name.required' => 'Nama pimpinan lokal wajib diisi.',
                'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
                'meeting_time.required' => 'Waktu pertemuan wajib diisi.',
                'meeting_time.regex' => 'Format waktu pertemuan tidak valid.',
                'location.required' => 'Tempat / ruangan wajib diisi.',
            ]);

            $datetime = $this->combineDateAndTime($validated['meeting_date'], $validated['meeting_time']);

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
                'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'location' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
            ], [
                'title.required' => 'Nama kegiatan wajib diisi.',
                'start_date.required' => 'Hari dan tanggal pelaksanaan wajib diisi.',
                'start_time.required' => 'Waktu pelaksanaan wajib diisi.',
                'start_time.regex' => 'Format waktu pelaksanaan tidak valid.',
                'location.required' => 'Lokasi kegiatan wajib diisi.',
            ]);

            $datetime = $this->combineDateAndTime($validated['start_date'], $validated['start_time']);

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

    private function combineDateAndTime(string $date, string $time): string
    {
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ':00';
        }

        return $date.' '.$time;
    }
}
