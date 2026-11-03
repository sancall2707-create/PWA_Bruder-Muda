<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Cloudflare & Local reverse proxies
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: function () {
                $user = auth()->user();
                if (! $user) {
                    return '/login';
                }

                return match ($user->role) {
                    'super_admin' => '/super-admin',
                    'admin' => '/admin',
                    default => '/dashboard',
                };
            },
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sesi/token CSRF kedaluwarsa pada pengunjung yang belum login (mis. halaman
        // login dibiarkan terbuka lama): arahkan kembali ke login dengan pesan ramah,
        // bukan halaman galat mentah "419 PAGE EXPIRED".
        // Laravel mengubah TokenMismatchException menjadi HttpException 419 sebelum
        // callback render dipanggil, jadi yang ditangkap adalah HttpException berstatus 419.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson() || auth()->check()) {
                return null;
            }

            return redirect()
                ->route('login')
                ->withInput($request->only('username', 'remember'))
                ->withErrors(['username' => 'Halaman login kedaluwarsa. Silakan masukkan kembali username dan kata sandi.']);
        });
    })->create();
