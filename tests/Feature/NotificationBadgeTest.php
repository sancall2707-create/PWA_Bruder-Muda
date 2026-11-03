<?php

namespace Tests\Feature;

use App\Models\DirectMessage;
use App\Models\PersonalAgenda;
use App\Models\ProvincialMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    private function bruder(string $username): User
    {
        return User::factory()->create(['role' => 'user', 'is_active' => true, 'username' => $username]);
    }

    public function test_tamu_ditolak(): void
    {
        $this->getJson(route('notifikasi.ringkasan'))->assertUnauthorized();
    }

    public function test_ringkasan_hanya_memuat_data_milik_pengguna_sendiri(): void
    {
        $a = $this->bruder('aaa');
        $b = $this->bruder('bbb');

        PersonalAgenda::create(['user_id' => $a->id, 'title' => 'A', 'start_at' => now()->addDay(), 'location' => 'x', 'status' => 'scheduled']);
        PersonalAgenda::create(['user_id' => $b->id, 'title' => 'B1', 'start_at' => now()->addDay(), 'location' => 'x', 'status' => 'scheduled']);
        PersonalAgenda::create(['user_id' => $b->id, 'title' => 'B2', 'start_at' => now()->addDays(2), 'location' => 'x', 'status' => 'scheduled']);
        PersonalAgenda::create(['user_id' => $b->id, 'title' => 'Lampau', 'start_at' => now()->subDays(5), 'location' => 'x', 'status' => 'scheduled']);

        DirectMessage::create(['sender_id' => $b->id, 'recipient_id' => $a->id, 'title' => 't', 'content' => 'c']);
        DirectMessage::create(['sender_id' => $a->id, 'recipient_id' => $b->id, 'title' => 't', 'content' => 'c']);

        $resp = $this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->assertOk();

        $resp->assertJsonCount(1, 'agenda');
        $resp->assertJsonPath('pesan', 1);
        $resp->assertJsonCount(1, 'pesan_daftar');
        $resp->assertJsonPath('agenda.0.t', 'A');
        $this->assertStringContainsString('no-store', $resp->headers->get('Cache-Control'));
    }

    public function test_pesan_dibaca_membuat_angka_hilang(): void
    {
        $a = $this->bruder('aaa');
        $msg = DirectMessage::create(['sender_id' => $a->id, 'recipient_id' => $a->id, 'title' => 't', 'content' => 'c']);

        $this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->assertJsonPath('pesan', 1);
        $this->actingAs($a)->get(route('pesan.show', $msg))->assertOk();
        $this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->assertJsonPath('pesan', 0);
    }

    public function test_sapaan_dan_pengumuman_aktif_terdaftar(): void
    {
        $a = $this->bruder('aaa');
        $s = ProvincialMessage::create(['title' => 'S', 'kind' => 'sapaan', 'content' => 'x', 'is_active' => true, 'published_at' => now()]);
        $p = ProvincialMessage::create(['title' => 'P', 'kind' => 'pengumuman', 'content' => 'x', 'is_active' => true, 'published_at' => now()]);
        ProvincialMessage::create(['title' => 'N', 'kind' => 'pengumuman', 'content' => 'x', 'is_active' => false, 'published_at' => now()]);

        $ids = collect($this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->json('sapaan'))->pluck('id')->all();

        $this->assertEqualsCanonicalizing(['s'.$s->id, 'p'.$p->id], $ids);
    }

    public function test_endpoint_tidak_menulis_ke_database(): void
    {
        $a = $this->bruder('aaa');
        PersonalAgenda::create(['user_id' => $a->id, 'title' => 'A', 'start_at' => now()->addDay(), 'location' => 'x', 'status' => 'scheduled']);
        DirectMessage::create(['sender_id' => $a->id, 'recipient_id' => $a->id, 'title' => 't', 'content' => 'c']);

        $sebelum = [PersonalAgenda::count(), DirectMessage::count(), DirectMessage::whereNull('read_at')->count(), User::count()];
        $this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->assertOk();
        $this->actingAs($a)->getJson(route('notifikasi.ringkasan'))->assertOk();
        $sesudah = [PersonalAgenda::count(), DirectMessage::count(), DirectMessage::whereNull('read_at')->count(), User::count()];

        $this->assertSame($sebelum, $sesudah);
    }

    public function test_beranda_memuat_penanda_dan_skrip(): void
    {
        $a = $this->bruder('aaa');
        $html = $this->actingAs($a)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('data-notif="agenda"', $html);
        $this->assertStringContainsString('data-notif="pesan"', $html);
        $this->assertStringContainsString('id="notifBell"', $html);
        $this->assertStringContainsString('data-notif="total"', $html);
        $this->assertStringContainsString('id="notifPanel"', $html);
        $this->assertStringContainsString('/js/notifikasi.js', $html);
        $this->assertStringContainsString('data-uid="'.$a->id.'"', $html);
    }
}
