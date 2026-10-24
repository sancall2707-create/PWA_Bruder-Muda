<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_register_the_service_worker(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->get('/login')
            ->assertOk()
            ->assertSee("navigator.serviceWorker.register('/sw.js')", false)
            ->assertSee("postMessage('clear-private')", false);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee("navigator.serviceWorker.register('/sw.js')", false)
            ->assertDontSee("postMessage('clear-private')", false);
    }

    public function test_service_worker_caches_static_assets_images_and_pages(): void
    {
        $source = file_get_contents(public_path('sw.js'));

        $this->assertIsString($source);
        $this->assertStringContainsString('pwa-static-', $source);
        $this->assertStringContainsString('pwa-images-', $source);
        $this->assertStringContainsString('pwa-pages-', $source);
        $this->assertStringContainsString('/css/fic-pwa.css', $source);
        $this->assertStringContainsString('/images/logo-fic.png', $source);
        $this->assertStringContainsString("pathname === '/profil/foto'", $source);
        $this->assertStringContainsString('no-store', $source);
        $this->assertStringContainsString('clear-private', $source);
    }
}
