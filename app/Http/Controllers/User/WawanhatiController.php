<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WawanhatiController extends Controller
{
    /**
     * Tampilkan daftar laporan Wawanhati Pimpinan Lokal.
     * Catatan: Wawanhati BUKAN fitur chat/messaging.
     */
    public function index(): View
    {
        // TEMPORARY DISPLAY DATA
        // TODO: Replace with database query from WawanhatiReport::where('user_id', auth()->id())->latest()->get()
        $reports = [
            (object) [
                'id' => 1,
                'local_leader_name' => 'Br. Fransiskus Xaverius, FIC',
                'meeting_at' => '14 Okt 2026, 14:00 WIB',
                'location' => 'Ruang Pimpinan Komunitas FIC Solo',
                'report_content' => 'Evaluasi triwulan keterlibatan dalam karya sekolah, adaptasi hidup bersama komunitas, dan kesehatan jasmani.',
                'status' => 'Terkirim',
            ],
            (object) [
                'id' => 2,
                'local_leader_name' => 'Br. Fransiskus Xaverius, FIC',
                'meeting_at' => '10 Agu 2026, 09:30 WIB',
                'location' => 'Ruang Pimpinan Komunitas FIC Solo',
                'report_content' => 'Dialog pembinaan seputar pembagian tugas harian di biara dan pendampingan asrama.',
                'status' => 'Selesai',
            ],
        ];

        $summary = [
            'selesai' => 1,
            'terkirim' => 1,
        ];

        return view('user.wawanhati.index', compact('reports', 'summary'));
    }

    /**
     * Formulir tambah laporan Wawanhati baru.
     */
    public function create(): View
    {
        return view('user.wawanhati.create');
    }

    /**
     * Simpan laporan Wawanhati baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'meeting_date' => ['required', 'string'],
            'local_leader_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'report_content' => ['required', 'string'],
        ], [
            'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
            'local_leader_name.required' => 'Nama pimpinan lokal wajib diisi.',
            'location.required' => 'Tempat / ruangan wajib diisi.',
            'report_content.required' => 'Isi / catatan wawanhati wajib diisi.',
        ]);

        // TODO: Simpan ke database WawanhatiReport saat backend CRUD aktif
        return redirect()->route('wawanhati.index')->with('success', 'Laporan Wawanhati berhasil dikirim (Simulasi UI).');
    }
}
