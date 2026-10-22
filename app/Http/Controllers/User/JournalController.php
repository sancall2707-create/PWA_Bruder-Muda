<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SelfDevelopmentJournal;
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
        $journals = SelfDevelopmentJournal::where('user_id', auth()->id())
            ->latest('activity_date')
            ->get();

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
        $validated = $request->validate([
            'activity_date' => ['required', 'date'],
            'activity_name' => ['required', 'string', 'max:255'],
            'organizer' => ['required', 'string', 'max:255'],
            'speaker' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reflection' => ['required', 'string', 'min:200', 'max:500'],
        ], [
            'activity_date.required' => 'Hari dan tanggal kegiatan wajib diisi.',
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'organizer.required' => 'Penyelenggara wajib diisi.',
            'speaker.required' => 'Narasumber wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'reflection.required' => 'Refleksi singkat wajib diisi.',
            'reflection.min' => 'Refleksi singkat minimal 200 karakter.',
            'reflection.max' => 'Refleksi singkat maksimal 500 karakter.',
        ]);

        SelfDevelopmentJournal::create([
            'user_id' => auth()->id(),
            'activity_date' => $validated['activity_date'],
            'activity_name' => $validated['activity_name'],
            'organizer' => $validated['organizer'],
            'speaker' => $validated['speaker'],
            'description' => $validated['description'],
            'reflection' => $validated['reflection'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal pengembangan diri berhasil dipublikasikan.');
    }

    /**
     * Formulir edit jurnal pengembangan diri.
     */
    public function edit(SelfDevelopmentJournal $journal): View
    {
        abort_unless($journal->user_id === auth()->id(), 404);

        return view('user.jurnal.edit', compact('journal'));
    }

    /**
     * Perbarui jurnal pengembangan diri.
     */
    public function update(Request $request, SelfDevelopmentJournal $journal): RedirectResponse
    {
        abort_unless($journal->user_id === auth()->id(), 404);

        $validated = $request->validate([
            'activity_date' => ['required', 'date'],
            'activity_name' => ['required', 'string', 'max:255'],
            'organizer' => ['required', 'string', 'max:255'],
            'speaker' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reflection' => ['required', 'string', 'min:200', 'max:500'],
        ], [
            'activity_date.required' => 'Hari dan tanggal kegiatan wajib diisi.',
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'organizer.required' => 'Penyelenggara wajib diisi.',
            'speaker.required' => 'Narasumber wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'reflection.required' => 'Refleksi singkat wajib diisi.',
            'reflection.min' => 'Refleksi singkat minimal 200 karakter.',
            'reflection.max' => 'Refleksi singkat maksimal 500 karakter.',
        ]);

        $journal->update($validated);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal pengembangan diri berhasil diperbarui.');
    }

    /**
     * Hapus jurnal pengembangan diri.
     */
    public function destroy(SelfDevelopmentJournal $journal): RedirectResponse
    {
        abort_unless($journal->user_id === auth()->id(), 404);

        $journal->delete();

        return redirect()->route('jurnal.index')->with('success', 'Jurnal pengembangan diri berhasil dihapus.');
    }
}
