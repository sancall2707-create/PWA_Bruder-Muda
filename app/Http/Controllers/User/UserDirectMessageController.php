<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserDirectMessageController extends Controller
{
    /**
     * Tampilkan daftar pesan khusus untuk user.
     */
    public function index(Request $request): View
    {
        $messages = DirectMessage::with('sender')
            ->where('recipient_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('user.pesan.index', compact('messages'));
    }

    /**
     * Tampilkan detail pesan khusus dan tandai sudah dibaca.
     */
    public function show(DirectMessage $message): View
    {
        abort_unless($message->recipient_id === auth()->id(), 403);

        if ($message->read_at === null) {
            $message->read_at = now();
            $message->save();
        }

        return view('user.pesan.show', compact('message'));
    }
}
