<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SpiritualGuidanceReport;
use App\Models\SpiritualGuidanceSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpiritualGuidanceController extends Controller
{
    /**
     * Tampilkan daftar laporan Bimbingan Rohani.
     */
    public function index(): View
    {
        $reports = SpiritualGuidanceReport::where('user_id', auth()->id())
            ->latest('meeting_at')
            ->get();

        $summary = [
            'selesai' => $reports->whereIn('status', ['completed', 'approved', 'published', 'Selesai'])->count(),
            'menunggu' => $reports->whereIn('status', ['submitted', 'draft', 'Terkirim', 'scheduled'])->count(),
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
     * Simpan laporan Bimbingan Rohani ke Database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_date' => ['required', 'date', 'before_or_equal:today'],
            'mentor_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'report_content' => ['required', 'string', 'min:200', 'max:500'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
            'meeting_date.before_or_equal' => 'Tanggal pertemuan tidak boleh melebihi hari ini (hanya dapat memilih hari ini atau tanggal yang sudah lewat).',
            'mentor_name.required' => 'Nama pembimbing wajib diisi.',
            'location.required' => 'Tempat pertemuan wajib diisi.',
            'report_content.required' => 'Isi bimbingan / catatan wajib diisi.',
            'report_content.min' => 'Isi bimbingan / catatan refleksi minimal 200 karakter.',
            'report_content.max' => 'Isi bimbingan / catatan refleksi maksimal 500 karakter.',
            'proof_file.mimes' => 'Format file bukti harus berupa JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti maksimal 5MB.',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('logbooks/bimbingan', 'local');
        }

        // Otomatis tandai jadwal bimbingan rohani yang bersangkutan menjadi selesai (completed)
        $schedule = SpiritualGuidanceSchedule::where('user_id', auth()->id())
            ->whereDate('scheduled_at', $validated['meeting_date'])
            ->where('status', '!=', 'cancelled')
            ->first();

        if (! $schedule) {
            $schedule = SpiritualGuidanceSchedule::where('user_id', auth()->id())
                ->where('mentor_name', $validated['mentor_name'])
                ->where('status', 'scheduled')
                ->first();
        }

        if ($schedule) {
            $schedule->update(['status' => 'completed']);
        }

        SpiritualGuidanceReport::create([
            'user_id' => auth()->id(),
            'schedule_id' => $schedule?->id,
            'mentor_name' => $validated['mentor_name'],
            'meeting_at' => $validated['meeting_date'],
            'location' => $validated['location'],
            'report_content' => $validated['report_content'],
            'proof_file' => $proofPath,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('bimbingan.index')->with('success', 'Laporan Bimbingan Rohani berhasil dikirim dan tersimpan di sistem.');
    }

    /**
     * Formulir edit laporan Bimbingan Rohani.
     */
    public function edit(SpiritualGuidanceReport $report): View
    {
        abort_unless($report->user_id === auth()->id(), 404);

        return view('user.bimbingan.edit', compact('report'));
    }

    /**
     * Perbarui laporan Bimbingan Rohani.
     */
    public function update(Request $request, SpiritualGuidanceReport $report): RedirectResponse
    {
        abort_unless($report->user_id === auth()->id(), 404);

        $validated = $request->validate([
            'meeting_date' => ['required', 'date', 'before_or_equal:today'],
            'mentor_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'report_content' => ['required', 'string', 'min:200', 'max:500'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'meeting_date.required' => 'Hari dan tanggal pertemuan wajib diisi.',
            'meeting_date.before_or_equal' => 'Tanggal pertemuan tidak boleh melebihi hari ini (hanya dapat memilih hari ini atau tanggal yang sudah lewat).',
            'mentor_name.required' => 'Nama pembimbing wajib diisi.',
            'location.required' => 'Tempat pertemuan wajib diisi.',
            'report_content.required' => 'Isi bimbingan / catatan wajib diisi.',
            'report_content.min' => 'Isi bimbingan / catatan refleksi minimal 200 karakter.',
            'report_content.max' => 'Isi bimbingan / catatan refleksi maksimal 500 karakter.',
            'proof_file.mimes' => 'Format file bukti harus berupa JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti maksimal 5MB.',
        ]);

        $proofPath = $report->proof_file;
        if ($request->hasFile('proof_file')) {
            if ($this->isOwnedLogbook($report->proof_file)) {
                Storage::disk('local')->delete($report->proof_file);
            }
            $proofPath = $request->file('proof_file')->store('logbooks/bimbingan', 'local');
        }

        $report->update([
            'mentor_name' => $validated['mentor_name'],
            'meeting_at' => $validated['meeting_date'],
            'location' => $validated['location'],
            'report_content' => $validated['report_content'],
            'proof_file' => $proofPath,
        ]);

        return redirect()->route('bimbingan.index')->with('success', 'Laporan Bimbingan Rohani berhasil diperbarui.');
    }

    /**
     * Hapus laporan Bimbingan Rohani.
     */
    public function destroy(SpiritualGuidanceReport $report): RedirectResponse
    {
        abort_unless($report->user_id === auth()->id(), 404);

        $report->delete();

        return redirect()->route('bimbingan.index')->with('success', 'Laporan Bimbingan Rohani berhasil dihapus.');
    }

    public function proof(SpiritualGuidanceReport $report): StreamedResponse
    {
        abort_unless($report->user_id === auth()->id(), 404);
        abort_unless($this->isOwnedLogbook($report->proof_file), 404);

        return Storage::disk('local')->download($report->proof_file);
    }

    private function isOwnedLogbook(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, 'logbooks/bimbingan/')
            && Storage::disk('local')->exists($path);
    }
}
