<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProvincialMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProvincialMessageManagementController extends Controller
{
    /**
     * Daftar & form sapaan Provinsial/TPPBM.
     */
    public function index(): View
    {
        $messages = ProvincialMessage::latest()->take(10)->get();

        return view('super-admin.sapaan.index', compact('messages'));
    }

    /**
     * Simpan sapaan baru atau pengumuman massal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sapaan-provinsial', 'public');
        }

        $message = ProvincialMessage::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_name' => $validated['author_name'] ?? auth()->user()->name,
            'image' => $imagePath,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'published_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if ($message->is_active) {
            ProvincialMessage::where('id', '!=', $message->id)->update(['is_active' => false]);
        }

        return redirect()->route('superadmin.sapaan.index')
            ->with('success', 'Sapaan / pengumuman berhasil dipublikasikan.');
    }
}
