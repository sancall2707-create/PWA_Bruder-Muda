<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Bruder Muda.
     */
    public function index(): View
    {
        $user = auth()->user();

        return view('user.profil.index', compact('user'));
    }

    /**
     * Formulir ubah profil akun yang sedang masuk.
     */
    public function edit(): View
    {
        $user = auth()->user();

        return view('user.profil.edit', compact('user'));
    }

    /**
     * Simpan profil milik pengguna yang sedang masuk.
     * Role, status aktif, username, dan id tidak diubah dari permintaan ini.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'community_location' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'community_location.max' => 'Komunitas maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',
        ]);

        $user->name = $validated['name'];

        if ($request->exists('community_location')) {
            $user->community_location = $validated['community_location'] ?? null;
        }

        if ($request->exists('email')) {
            $user->email = $validated['email'] ?? null;
        }

        $user->save();

        return redirect()
            ->route('profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Formulir ubah kata sandi.
     */
    public function editPassword(): View
    {
        return view('user.profil.kata-sandi');
    }

    /**
     * Simpan kata sandi baru lewat cast hashed. Sesi login saat ini tetap aktif.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('profil')
            ->with('success', 'Kata sandi berhasil diubah. Anda tetap masuk.');
    }
}
