<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Tampilkan daftar Jurnal Pengembangan Diri Bruder Muda.
     */
    public function index(): View
    {
        // TEMPORARY DISPLAY DATA
        // TODO: Replace with query from SelfDevelopmentJournal::where('user_id', auth()->id())->latest()->get()
        $journals = [
            (object) [
                'id' => 1,
                'activity_date' => '05 Okt 2026',
                'activity_name' => 'Retret Tahunan Bruder Muda FIC Indonesia',
                'organizer' => 'Dewan Pimpinan Provinsi FIC Indonesia',
                'speaker' => 'Rm. Gregorius, SJ',
                'description' => 'Pendalaman tema spiritualitas penyerahan diri dan keteguhan hati dalam menjawab panggilan zaman kontemporer.',
                'reflection' => 'Merenungkan kembali semangat awal pendiri Kongregasi, Mgr. Ludovicus Rutten dan Bernardus Hoecken. Saya diajak untuk lebih terbuka mendengarkan bimbingan Roh Kudus dan menempatkan kasih persaudaraan sebagai dasar pelayanan.',
                'status' => 'Selesai',
            ],
            (object) [
                'id' => 2,
                'activity_date' => '22 Sep 2026',
                'activity_name' => 'Lokakarya Pedagogi Transformatif & Karakter Kristiani',
                'organizer' => 'Yayasan Pangudi Luhur Pusat',
                'speaker' => 'Dr. Paulina Wulandari, M.Pd.',
                'description' => 'Pelatihan metode pengajaran yang mengedepankan pendekatan personal kepada peserta didik di sekolah-sekolah FIC.',
                'reflection' => 'Menyadari bahwa tugas pendidik bukan hanya mentransfer ilmu pengetahuan, melainkan menghadirkan wajah Kristus yang mendidik dan memulihkan.',
                'status' => 'Terkirim',
            ],
        ];

        return view('user.jurnal.index', compact('journals'));
    }

    /**
     * Formulir tambah jurnal pengembangan diri baru.
     */
    public function create(): View
    {
        return view('user.jurnal.create');
    }

    /**
     * Simpan jurnal pengembangan diri.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'activity_date' => ['required', 'string'],
            'activity_name' => ['required', 'string', 'max:255'],
            'organizer' => ['required', 'string', 'max:255'],
            'speaker' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reflection' => ['required', 'string'],
        ], [
            'activity_date.required' => 'Hari dan tanggal kegiatan wajib diisi.',
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'organizer.required' => 'Penyelenggara wajib diisi.',
            'speaker.required' => 'Narasumber wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'reflection.required' => 'Refleksi singkat wajib diisi.',
        ]);

        // TODO: Simpan ke database SelfDevelopmentJournal saat backend CRUD aktif
        return redirect()->route('jurnal.index')->with('success', 'Jurnal pengembangan diri berhasil dipublikasikan (Simulasi UI).');
    }
}
