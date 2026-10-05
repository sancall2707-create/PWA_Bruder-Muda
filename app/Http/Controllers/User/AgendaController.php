<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        // TEMPORARY DISPLAY DATA
        // TODO: Replace with consolidated query from SpiritualGuidanceSchedule, WawanhatiSchedule, and PersonalAgenda
        $agendas = [
            (object) [
                'id' => 1,
                'category' => 'bimbingan',
                'title' => 'Bimbingan Rohani Bulanan',
                'target_person' => 'Br. Yohanes Subagio, FIC',
                'date_formatted' => '12 Okt 2026',
                'time_formatted' => '10:00 WIB',
                'location' => 'Ruang Bimbingan Ambarawa',
                'notes' => 'Topik: Pembacaan tanda zaman dan kesetiaan doa batin.',
                'status' => 'Terjadwal',
            ],
            (object) [
                'id' => 2,
                'category' => 'wawanhati',
                'title' => 'Wawanhati Pimpinan Lokal',
                'target_person' => 'Br. Fransiskus Xaverius, FIC',
                'date_formatted' => '14 Okt 2026',
                'time_formatted' => '14:00 WIB',
                'location' => 'Ruang Piko Solo',
                'notes' => 'Evaluasi karya pendidikan dan dinamika hidup berkomunitas.',
                'status' => 'Terjadwal',
            ],
            (object) [
                'id' => 3,
                'category' => 'lainnya',
                'title' => 'Ibadat Bersama Komunitas Distrik',
                'target_person' => 'Komunitas Biara FIC',
                'date_formatted' => '20 Okt 2026',
                'time_formatted' => '09:00 WIB',
                'location' => 'Kapel Biara Pusat Semarang',
                'notes' => 'Misa syukur peringatan Santo Pelindung dan ramah tamah.',
                'status' => 'Terjadwal',
            ],
        ];

        return view('user.agenda.index', compact('agendas'));
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
     * Simpan jadwal / agenda baru (simulasi UI).
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('agenda_type', 'lainnya');

        // TODO: Simpan ke tabel masing-masing (spiritual_guidance_schedules, wawanhati_schedules, atau personal_agendas)
        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil ditambahkan ke jadwal Anda (Simulasi UI).');
    }
}
