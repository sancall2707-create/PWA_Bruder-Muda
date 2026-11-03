<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LihatKalenderButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_dashboard_shows_lihat_kalender_as_button_without_arrow(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertSee('Lihat Kalender');
        $response->assertDontSee('Lihat Kalender →', false);
        $response->assertSee(route('superadmin.monitoring.agenda'), false);
    }

    public function test_admin_dashboard_shows_lihat_kalender_as_button_without_arrow(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Lihat Kalender');
        $response->assertDontSee('Lihat Kalender →', false);
        $response->assertSee(route('superadmin.monitoring.agenda'), false);
    }
}
