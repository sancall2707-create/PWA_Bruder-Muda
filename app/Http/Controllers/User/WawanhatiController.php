<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WawanhatiReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WawanhatiController extends Controller
{
    /**
     * Tampilkan daftar laporan Wawanhati Pimpinan Lokal.
     */
    public function index(): View
    {
        $reports = WawanhatiReport::where('user_id', auth()->id())
            ->latest('meeting_at')
            ->get();

        $summary = [
            'selesai' => $reports->whereIn('status', ['completed', 'approved', 'published', 'Selesai'])->count(),
            'terkirim' => $reports->whereIn('status', ['submitted', 'draft', 'Terkirim', 'scheduled'])->count(),
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
        $validated = $request->validate([
            'meeting_date' => ['required', 'date'],
            'local_leader_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'report_content' => ['required', 'string', 'min:200', 'max:500'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
            'local_leader_name.required' => 'Nama pimpinan lokal wajib diisi.',
            'location.required' => 'Tempat / ruangan wajib diisi.',
            'report_content.required' => 'Isi / catatan wawanhati wajib diisi.',
            'report_content.min' => 'Isi catatan wawanhati minimal 200 karakter.',
            'report_content.max' => 'Isi catatan wawanhati maksimal 500 karakter.',
            'proof_file.mimes' => 'Format file bukti harus berupa JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti maksimal 5MB.',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('logbooks/wawanhati', 'local');
        }

        WawanhatiReport::create([
            'user_id' => auth()->id(),
            'local_leader_name' => $validated['local_leader_name'],
            'meeting_at' => $validated['meeting_date'],
            'location' => $validated['location'],
            'report_content' => $validated['report_content'],
            'proof_file' => $proofPath,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('wawanhati.index')->with('success', 'Laporan Wawanhati Pimpinan Lokal berhasil dikirim.');
    }

    public function proof(WawanhatiReport $report): StreamedResponse
    {
        abort_unless($report->user_id === auth()->id(), 404);
        abort_unless($this->isOwnedLogbook($report->proof_file), 404);

        return Storage::disk('local')->download($report->proof_file);
    }

    private function isOwnedLogbook(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, 'logbooks/wawanhati/')
            && Storage::disk('local')->exists($path);
    }
}
