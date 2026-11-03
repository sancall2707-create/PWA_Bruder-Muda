<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\PersonalAgenda;
use App\Models\ProvincialMessage;
use Illuminate\Http\JsonResponse;

/**
 * Ringkasan penanda notifikasi (hanya membaca, tidak menulis apa pun ke database).
 *
 * - pesan : pesan khusus belum dibaca (memakai kolom read_at yang sudah ada).
 * - agenda: agenda mendatang milik pengguna (status "sudah dibuka" dicatat di HP).
 * - sapaan: sapaan aktif dan pengumuman aktif (status "sudah dibuka" dicatat di HP).
 */
class NotificationSummaryController extends Controller
{
    public function summary(): JsonResponse
    {
        $user = auth()->user();

        $pesan = DirectMessage::query()
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->limit(20)
            ->get(['id', 'title'])
            ->map(fn (DirectMessage $m) => [
                'id' => $m->id,
                't' => (string) $m->title,
                'u' => route('pesan.show', $m),
            ])
            ->values();

        $agenda = PersonalAgenda::query()
            ->where('user_id', $user->id)
            ->where('start_at', '>=', now()->startOfDay())
            ->orderBy('start_at')
            ->limit(100)
            ->get(['id', 'title', 'start_at', 'created_at'])
            ->map(fn (PersonalAgenda $a) => [
                'id' => $a->id,
                'c' => $a->created_at?->timestamp ?? 0,
                't' => (string) $a->title,
                'w' => $a->start_at?->copy()->locale('id')->translatedFormat('j M Y, H:i').' WIB',
                'u' => route('agenda.index'),
            ])
            ->values();

        $sapaan = collect();
        $current = ProvincialMessage::currentSapaan();
        if ($current) {
            $sapaan->push([
                'id' => 's'.$current->id,
                'c' => $current->published_at?->timestamp ?? $current->created_at?->timestamp ?? 0,
                't' => (string) $current->title,
                'k' => $current->sapaanLabel(),
                'u' => route('sapaan-provinsial'),
            ]);
        }
        foreach (ProvincialMessage::currentAnnouncements() as $item) {
            $sapaan->push([
                'id' => 'p'.$item->id,
                'c' => $item->published_at?->timestamp ?? $item->created_at?->timestamp ?? 0,
                't' => (string) $item->title,
                'k' => 'Pengumuman',
                'u' => route('sapaan-provinsial'),
            ]);
        }

        return response()
            ->json([
                'pesan' => $user->unreadDirectMessagesCount(),
                'pesan_daftar' => $pesan,
                'agenda' => $agenda,
                'sapaan' => $sapaan->values(),
                'sekarang' => now()->timestamp,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private');
    }
}
