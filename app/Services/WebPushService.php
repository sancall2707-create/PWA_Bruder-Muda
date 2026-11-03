<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Mengirim Web Push ke semua perangkat pengguna tertentu atau ke semua pengguna aktif (role=user).
 * Tidak menulis ke database kecuali menghapus langganan yang sudah tidak valid (expired/unsubscribed).
 */
class WebPushService
{
    private function webPush(): WebPush
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        return new WebPush($auth, [], 30, ['verify' => true]);
    }

    /**
     * Kirim notifikasi ke satu pengguna (semua perangkatnya).
     */
    public function sendToUser(int $userId, string $title, string $body, ?string $url = null, ?string $tag = null): int
    {
        $subs = PushSubscription::where('user_id', $userId)->get();
        if ($subs->isEmpty()) {
            return 0;
        }

        return $this->send($subs, $title, $body, $url, $tag);
    }

    /**
     * Kirim notifikasi ke semua pengguna aktif (role=user).
     */
    public function broadcast(string $title, string $body, ?string $url = null, ?string $tag = null): int
    {
        $subs = PushSubscription::query()
            ->whereHas('user', fn ($q) => $q->where('role', 'user')->where('is_active', true)->whereNull('deleted_at'))
            ->get();

        if ($subs->isEmpty()) {
            return 0;
        }

        return $this->send($subs, $title, $body, $url, $tag);
    }

    private function send($subs, string $title, string $body, ?string $url, ?string $tag): int
    {
        $wp = $this->webPush();
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
            'tag' => $tag,
            'icon' => '/images/logo-fic.png',
        ]);

        foreach ($subs as $sub) {
            $wp->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]),
                $payload
            );
        }

        $sent = 0;
        foreach ($wp->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                // Langganan sudah tidak valid → hapus dari database
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)->delete();
            }
        }

        return $sent;
    }
}
