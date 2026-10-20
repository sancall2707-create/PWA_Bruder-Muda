<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DirectMessageController extends Controller
{
    /**
     * Tampilkan form kirim pesan satu arah untuk bruder.
     */
    public function create(User $user): View
    {
        abort_unless(in_array($user->role, ['user', 'admin'], true), 404);

        $sentMessages = DirectMessage::where('recipient_id', $user->id)
            ->where('sender_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('super-admin.users.messages.create', compact('user', 'sentMessages'));
    }

    /**
     * Kirim pesan satu arah ke bruder.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['user', 'admin'], true), 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ], [
            'title.required' => 'Judul pesan wajib diisi.',
            'content.required' => 'Isi pesan wajib diisi.',
        ]);

        DirectMessage::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', "Pesan khusus berhasil dikirimkan ke {$user->name}.");
    }

    /**
     * Tarik pesan yang salah kirim agar tidak lagi tampil di akun bruder.
     */
    public function destroy(User $user, DirectMessage $message): RedirectResponse
    {
        abort_unless($message->recipient_id === $user->id, 404);

        $actor = auth()->user();
        $canRetract = $actor->role === 'super_admin' || $message->sender_id === $actor->id;
        abort_unless($canRetract, 403);

        $message->delete();

        return redirect()->back()
            ->with('success', 'Pesan berhasil ditarik. Bruder tidak lagi melihat pesan ini.');
    }
}
