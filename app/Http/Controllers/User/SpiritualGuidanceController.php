<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SpiritualGuidanceController extends Controller
{
    /**
     * Tampilkan daftar laporan Bimbingan Rohani.
     */
    public function index(): View
    {
        // TEMPORARY DISPLAY DATA
        // TODO: Replace with database data from SpiritualGuidanceReport::where('user_id', auth()->id())->latest()->get()
        $reports = [
            (object) [
                'id' => 1,
                'mentor_name' => 'Br. Yohanes Subagio, FIC',
                'meeting_at' => '12 Okt 2026, 10:00 WIB',
                'location' => 'Ruang Bimbingan Komunitas Ambarawa',
                'report_content' => 'Mendiskusikan perkembangan hidup doa pribadi, dinamika komunitas harian, dan tantangan dalam karya kerasulan.',
                'status' => 'Selesai',
            ],
            (object) [
                'id' => 2,
                'mentor_name' => 'Br. Yohanes Subagio, FIC',
                'meeting_at' => '18 Sep 2026, 14:00 WIB',
                'location' => 'Wisma Pembinaan FIC Semarang',
                'report_content' => 'Refleksi atas kaul ketaatan dan kesiapan mengemban tugas studi dan perutusan baru.',
                'status' => 'Terkirim',
            ],
        ];

        $summary = [
            'selesai' => 1,
            'menunggu' => 1,
        ];

        return view('user.bimbingan.index', compact('reports', 'summary'));
    }

    /**
     * Formulir tambah laporan Bimbingan Rohani baru.
     */
    public function create(): View
    {
        return view('user.bimbingan.create');
    }

    /**
     * Simpan laporan Bimbingan Rohani.
     * UI Phase: Safe validation and placeholder storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'meeting_date' => ['required', 'string'],
            'mentor_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'report_content' => ['required', 'string'],
        ], [
            'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
            'mentor_name.required' => 'Nama pembimbing wajib diisi.',
            'location.required' => 'Tempat pertemuan wajib diisi.',
            'report_content.required' => 'Isi bimbingan / catatan wajib diisi.',
        ]);

        // TODO: Simpan ke database SpiritualGuidanceReport saat backend CRUD aktif
        return redirect()->route('bimbingan.index')->with('success', 'Laporan Bimbingan Rohani berhasil dikirim (Simulasi UI).');
    }
}
