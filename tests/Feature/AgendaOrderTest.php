<?php

namespace Tests\Feature;

use App\Models\PersonalAgenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_upcoming_sorted_nearest_first_then_past_newest_first(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 3)->setTime(8, 0));

        $user = User::factory()->create(['role' => 'user']);

        // Dibuat sengaja tidak berurutan: yang tanggal 8 dibuat lebih dulu/terbaru.
        $judulLewat = ['Agenda Lewat Lama' => '2026-11-01 09:00:00', 'Agenda Lewat Baru' => '2026-11-02 09:00:00'];
        $judulAkan = ['Agenda Tgl 8' => '2026-11-08 09:00:00', 'Agenda Tgl 5' => '2026-11-05 09:00:00', 'Agenda Tgl 20' => '2026-11-20 09:00:00'];

        foreach (array_merge($judulAkan, $judulLewat) as $judul => $waktu) {
            PersonalAgenda::create([
                'user_id' => $user->id,
                'title' => $judul,
                'start_at' => $waktu,
                'location' => 'Deltamas',
                'status' => 'scheduled',
            ]);
        }

        $response = $this->actingAs($user)->get(route('agenda.index'));
        $response->assertOk();

        $response->assertSeeInOrder([
            'Agenda Tgl 5',
            'Agenda Tgl 8',
            'Agenda Tgl 20',
            'Agenda Lewat Baru',
            'Agenda Lewat Lama',
        ]);
    }
}
