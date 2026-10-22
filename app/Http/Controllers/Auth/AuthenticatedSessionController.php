<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman formulir login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Tangani proses autentikasi login.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi harus berupa teks.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());

        // Proteksi brute-force: maksimal 5 percobaan gagal per 1 menit
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $username = $request->input('username');
        $password = $request->input('password');

        // Cari user aktif dan bukan soft-deleted (SoftDeletes bawaan Eloquent menyaring deleted_at != null)
        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'Username atau kata sandi tidak sesuai.',
            ]);
        }

        // Cek status aktif user
        if (! $user->is_active) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'Akun tidak dapat digunakan. Silakan hubungi administrator.',
            ]);
        }

        // Reset rate limiter setelah berhasil lolos validasi kredensial dan status aktif
        RateLimiter::clear($throttleKey);

        // Login user ke web session
        Auth::login($user, $request->boolean('remember'));

        // Regenerasi ID session untuk mencegah session fixation
        $request->session()->regenerate();

        // Redirect sesuai role autentikasi
        return $this->redirectByRole($user->role);
    }

    /**
     * Tangani proses logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Tentukan URL redirect berdasarkan role pengguna.
     */
    protected function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'super_admin' => redirect('/super-admin'),
            'admin', 'admin_ketua_tppbm' => redirect('/admin'),
            'user' => redirect('/sapaan-provinsial'),
            default => redirect('/dashboard'),
        };
    }
}
