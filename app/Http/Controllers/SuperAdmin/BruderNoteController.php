<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BruderNote;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BruderNoteController extends Controller
{
    /**
     * Tampilkan formulir Tambah Catatan Baru untuk bruder.
     */
    public function create(User $user): View
    {
        abort_unless($user->role === 'user', 404);

        return view('super-admin.users.notes.create', [
            'user' => $user,
            'categories' => BruderNote::CATEGORIES,
        ]);
    }

    /**
     * Simpan catatan baru ke database.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);

        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(BruderNote::CATEGORIES)],
            'content' => ['required', 'string'],
        ], [
            'category.required' => 'Pilih salah satu kategori catatan.',
            'category.in' => 'Kategori catatan tidak valid.',
            'content.required' => 'Uraian catatan wajib diisi.',
        ]);

        BruderNote::create([
            'user_id' => $user->id,
            'author_id' => auth()->id(),
            'category' => $validated['category'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Catatan berhasil ditambahkan!');
    }
}
