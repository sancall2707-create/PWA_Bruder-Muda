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

    public function test_subscribe_di_perangkat_sama_memindahkan_kepemilikan_user(): void
    {
        $userA = $this->bruder();
        $userB = $this->bruder();
        $endpoint = 'https://fcm.googleapis.com/fcm/send/shared_device';

        // User A login di HP
        $this->actingAs($userA)->postJson(route('push.subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'k1', 'auth' => 'a1'],
        ])->assertOk();

        $this->assertDatabaseHas('push_subscriptions', ['user_id' => $userA->id, 'endpoint' => $endpoint]);

        // User B login di HP yang sama tanpa tombol manual
        $this->actingAs($userB)->postJson(route('push.subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'k1', 'auth' => 'a1'],
        ])->assertOk();

        // User A tidak boleh lagi punya token di perangkat itu, sekarang milik User B
        $this->assertDatabaseMissing('push_subscriptions', ['user_id' => $userA->id, 'endpoint' => $endpoint]);
        $this->assertDatabaseHas('push_subscriptions', ['user_id' => $userB->id, 'endpoint' => $endpoint]);
    }

    public function test_vapid_key_bisa_dibaca_tanpa_login(): void
    {
        // Service Worker merotasi token tanpa halaman terbuka, jadi tidak ada sesi login.
        $this->getJson(route('push.vapid'))->assertOk()->assertJsonStructure(['key']);
    }

    public function test_rotasi_token_memindahkan_langganan_ke_endpoint_baru(): void
    {
        $u = $this->bruder();
        $lama = 'https://fcm.googleapis.com/fcm/send/token_lama';
        $baru = 'https://fcm.googleapis.com/fcm/send/token_baru';

        PushSubscription::create(['user_id' => $u->id, 'endpoint' => $lama, 'p256dh' => 'p_lama', 'auth' => 'a_lama']);

        // Tanpa sesi login dan tanpa token CSRF, seperti saat Service Worker bekerja
        $this->postJson(route('push.rotate'), [
            'endpoint_lama' => $lama,
            'endpoint' => $baru,
            'keys' => ['p256dh' => 'p_baru', 'auth' => 'a_baru'],
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => $lama]);
        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => $baru,
            'user_id' => $u->id,
            'p256dh' => 'p_baru',
        ]);
        $this->assertSame(1, PushSubscription::where('user_id', $u->id)->count());
    }

    public function test_rotasi_ditolak_bila_endpoint_lama_tidak_dikenal(): void
    {
        $this->postJson(route('push.rotate'), [
            'endpoint_lama' => 'https://fcm.googleapis.com/fcm/send/palsu',
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/baru',
            'keys' => ['p256dh' => 'p', 'auth' => 'a'],
        ])->assertNotFound();

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_rotasi_tidak_bisa_memindahkan_token_ke_akun_lain(): void
    {
        $korban = $this->bruder();
        $penyerang = $this->bruder();
        $lama = 'https://fcm.googleapis.com/fcm/send/milik_korban';

        PushSubscription::create(['user_id' => $korban->id, 'endpoint' => $lama, 'p256dh' => 'p', 'auth' => 'a']);

        // Penyerang sudah login, tetap tidak bisa mengklaim token korban:
        // kepemilikan diambil dari baris lama di database, bukan dari sesi pemanggil.
        $this->actingAs($penyerang)->postJson(route('push.rotate'), [
            'endpoint_lama' => $lama,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/milik_penyerang',
            'keys' => ['p256dh' => 'p2', 'auth' => 'a2'],
        ])->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/milik_penyerang',
            'user_id' => $korban->id,
        ]);
        $this->assertDatabaseMissing('push_subscriptions', ['user_id' => $penyerang->id]);
    }

    public function test_panel_super_admin_memuat_skrip_notifikasi_tanpa_lonceng(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $html = $this->actingAs($sa)->get('/super-admin')->assertOk()->getContent();

        // Skrip & token CSRF wajib ada agar token push admin ikut diselaraskan dan dicabut
        $this->assertStringContainsString('/js/notifikasi.js', $html);
        $this->assertStringContainsString('csrf-token', $html);
        $this->assertStringContainsString('data-push-subscribe', $html);

        // Tampilan panel admin tidak boleh berubah: tidak ada lonceng maupun panel baru
        $this->assertStringNotContainsString('id="notifBell"', $html);
        $this->assertStringNotContainsString('id="notifPanel"', $html);
    }

    public function test_admin_bisa_menyimpan_dan_mencabut_token_push(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $endpoint = 'https://fcm.googleapis.com/fcm/send/admin_device';

        $this->actingAs($admin)->postJson(route('push.subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'p', 'auth' => 'a'],
        ])->assertOk();

        $this->assertDatabaseHas('push_subscriptions', ['user_id' => $admin->id, 'endpoint' => $endpoint]);

        $this->actingAs($admin)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertDatabaseMissing('push_subscriptions', ['user_id' => $admin->id]);
    }
}
