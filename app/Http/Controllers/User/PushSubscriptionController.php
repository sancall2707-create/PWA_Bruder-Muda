<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan langganan push dari browser pengguna.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $currentUser = $request->user();

        // 1. Jika endpoint ini sebelumnya terdaftar atas nama user lain (misal di HP yang sama ganti akun),
        // hapus kaitan lama agar notifikasi user lama tidak nyasar ke user ini.
        PushSubscription::where('endpoint', $data['endpoint'])
            ->where('user_id', '!=', $currentUser->id)
            ->delete();

        // 2. Simpan atau perbarui langganan untuk user yang sedang aktif login saat ini
        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => $currentUser->id,
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
            ]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Hapus langganan push (pengguna mematikan notifikasi).
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url'],
        ]);

        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $data['endpoint'])
            ->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Kembalikan VAPID public key agar klien bisa berlangganan.
     */
    public function vapidKey(): JsonResponse
    {
        return response()->json([
            'key' => config('services.vapid.public_key'),
        ]);
    }

    /**
     * Perbarui langganan saat browser/FCM memutar (rotasi) token.
     *
     * Dipanggil oleh Service Worker lewat event `pushsubscriptionchange`, yang bisa
     * terjadi tanpa halaman terbuka sehingga tidak ada token CSRF maupun sesi login.
     * Permintaan mengamankan diri sendiri: pemanggil wajib menyertakan endpoint lama
     * yang sudah terdaftar di database. Nilai itu berupa URL acak panjang dari FCM yang
     * hanya diketahui perangkat pemilik, dan kepemilikan akun diambil dari baris lama
     * (bukan dari masukan pemanggil), jadi token tidak bisa dipindah ke akun lain.
     */
    public function rotate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint_lama' => ['required', 'string', 'max:500'],
            'endpoint' => ['required', 'url', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $lama = PushSubscription::where('endpoint', $data['endpoint_lama'])->first();

        if (! $lama) {
            return response()->json(['ok' => false, 'alasan' => 'langganan lama tidak dikenal'], 404);
        }

        $pemilik = $lama->user_id;

        // Bersihkan baris lama dan sisa baris dengan endpoint baru (milik siapa pun),
        // supaya tidak ada token kedaluwarsa yang tertinggal.
        PushSubscription::where('endpoint', $data['endpoint_lama'])->delete();
        PushSubscription::where('endpoint', $data['endpoint'])->delete();

        PushSubscription::create([
            'user_id' => $pemilik,
            'endpoint' => $data['endpoint'],
            'p256dh' => $data['keys']['p256dh'],
            'auth' => $data['keys']['auth'],
        ]);

        return response()->json(['ok' => true]);
    }
}
