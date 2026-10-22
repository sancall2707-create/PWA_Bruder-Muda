<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil dan form edit profil Super Admin / Admin.
     */
    public function index(): View
    {
        $user = auth()->user();

        return view('super-admin.profil.index', [
            'user' => $user,
            'headerTitle' => 'Profil & Edit Akun',
            'headerSubtitle' => 'Kelola profil, foto, dan kata sandi Anda',
        ]);
    }

    /**
     * Simpan pembaruan profil Super Admin.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'community_location' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'profile_photo.file' => 'Foto profil harus berupa berkas gambar.',
            'profile_photo.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->community_location = $validated['community_location'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        if ($request->hasFile('profile_photo')) {
            $stored = $request->file('profile_photo')->store('profil', 'public');
            if (is_string($stored)) {
                $oldPhoto = $user->profile_photo;
                $user->profile_photo = $stored;
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        } elseif ($request->boolean('remove_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = null;
        }

        $user->save();

        return redirect()->route('superadmin.profil')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi Super Admin.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $user->password = $validated['password'];
        $user->save();

        return redirect()->route('superadmin.profil')
            ->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
