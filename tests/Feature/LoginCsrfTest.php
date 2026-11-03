<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LoginCsrfTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_never_cached_by_browser(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
    }

    public function test_expired_csrf_token_redirects_guest_to_login_with_friendly_message(): void
    {
        Route::middleware('web')->post('/_uji-419', function () {
            throw new TokenMismatchException('CSRF token mismatch.');
        });

        $response = $this->from(route('login'))->post('/_uji-419', ['username' => 'bruder_uji']);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertStringContainsString(
            'kedaluwarsa',
            session('errors')->first('username')
        );
    }

    public function test_login_still_works_normally(): void
    {
        $user = User::factory()->create([
            'username' => 'bruder_login',
            'role' => 'user',
            'is_active' => true,
            'password' => bcrypt('rahasia123'),
        ]);

        $this->post(route('login.store'), [
            'username' => 'bruder_login',
            'password' => 'rahasia123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }
}
