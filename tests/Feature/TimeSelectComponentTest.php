<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeSelectComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_agenda_bimbingan_menampilkan_dropdown_jam_dan_menit(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('agenda.bimbingan.create'));

        $response->assertOk()
            ->assertSee('id="meeting_time_hour"', false)
            ->assertSee('id="meeting_time_minute"', false)
            ->assertSee('name="meeting_time"', false)
            ->assertSee('Jam 10')
            ->assertSee('Menit 00')
            ->assertDontSee('type="time"', false);
    }

    public function test_form_agenda_wawanhati_menampilkan_dropdown_jam_dan_menit(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('agenda.wawanhati.create'));

        $response->assertOk()
            ->assertSee('id="meeting_time_hour"', false)
            ->assertSee('id="meeting_time_minute"', false)
            ->assertSee('Jam 14')
            ->assertSee('Menit 00')
            ->assertDontSee('type="time"', false);
    }

    public function test_form_agenda_lainnya_menampilkan_dropdown_jam_dan_menit(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('agenda.lainnya.create'));

        $response->assertOk()
            ->assertSee('id="start_time_hour"', false)
            ->assertSee('id="start_time_minute"', false)
            ->assertSee('Jam 09')
            ->assertSee('Menit 00')
            ->assertDontSee('type="time"', false);
    }

    public function test_simpan_agenda_dengan_waktu_dari_dropdown_berhasil(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('agenda.store'), [
            'agenda_type' => 'bimbingan',
            'mentor_name' => 'Br. Yohanes Pembimbing, FIC',
            'meeting_date' => now()->addDays(2)->format('Y-m-d'),
            'meeting_time' => '15:35', // Hasil gabungan Jam 15 + Menit 35
            'location' => 'Ruang Bimbingan',
            'topic_notes' => 'Catatan persiapan bimbingan',
        ]);

        $response->assertRedirect(route('agenda.index'));

        $this->assertDatabaseHas('spiritual_guidance_schedules', [
            'user_id' => $user->id,
            'mentor_name' => 'Br. Yohanes Pembimbing, FIC',
            'location' => 'Ruang Bimbingan',
        ]);
    }
}
