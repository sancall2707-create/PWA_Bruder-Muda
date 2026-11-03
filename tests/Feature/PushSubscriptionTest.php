<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function bruder(): User
    {
        return User::factory()->create(['role' => 'user', 'is_active' => true]);
    }

    public function test_tamu_tidak_bisa_subscribe(): void
    {
        $this->postJson(route('push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys' => ['p256dh' => 'p', 'auth' => 'a'],
        ])->assertUnauthorized();
    }

    public function test_subscribe_membuat_catatan_baru(): void
    {
        $u = $this->bruder();
        $this->actingAs($u)->postJson(route('push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys' => ['p256dh' => 'p256dh_test', 'auth' => 'auth_test'],
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $u->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'p256dh' => 'p256dh_test',
            'auth' => 'auth_test',
        ]);
    }

    public function test_subscribe_duplikat_memperbarui_bukan_membuat_baru(): void
    {
        $u = $this->bruder();
        $data = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/xyz',
            'keys' => ['p256dh' => 'old', 'auth' => 'old'],
        ];
        $this->actingAs($u)->postJson(route('push.subscribe'), $data)->assertOk();
        $data['keys'] = ['p256dh' => 'new', 'auth' => 'new'];
        $this->actingAs($u)->postJson(route('push.subscribe'), $data)->assertOk();

        $this->assertSame(1, PushSubscription::where('endpoint', $data['endpoint'])->count());
        $this->assertDatabaseHas('push_subscriptions', ['p256dh' => 'new']);
    }

    public function test_unsubscribe_menghapus_catatan(): void
    {
        $u = $this->bruder();
        PushSubscription::create(['user_id' => $u->id, 'endpoint' => 'https://fcm.googleapis.com/fcm/send/del', 'p256dh' => 'p', 'auth' => 'a']);

        $this->actingAs($u)->postJson(route('push.unsubscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/del',
        ])->assertOk();

        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => 'https://fcm.googleapis.com/fcm/send/del']);
    }

    public function test_unsubscribe_hanya_milik_sendiri(): void
    {
        $a = $this->bruder();
        $b = $this->bruder();
        PushSubscription::create(['user_id' => $b->id, 'endpoint' => 'https://fcm.googleapis.com/fcm/send/b', 'p256dh' => 'p', 'auth' => 'a']);

        $this->actingAs($a)->postJson(route('push.unsubscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/b',
        ])->assertOk();

        // Masih ada karena bukan milik user a
        $this->assertDatabaseHas('push_subscriptions', ['endpoint' => 'https://fcm.googleapis.com/fcm/send/b']);
    }

    public function test_vapid_key_dikembalikan(): void
    {
        $u = $this->bruder();
        $resp = $this->actingAs($u)->getJson(route('push.vapid'))->assertOk();
        $resp->assertJsonStructure(['key']);
    }

    public function test_logout_membersihkan_push_subscriptions_milik_pengguna(): void
    {
        $u = $this->bruder();
        PushSubscription::create([
            'user_id' => $u->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/logout_test',
            'p256dh' => 'p',
            'auth' => 'a',
        ]);

        $this->actingAs($u)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertDatabaseMissing('push_subscriptions', [
            'user_id' => $u->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/logout_test',
        ]);
    }
}
