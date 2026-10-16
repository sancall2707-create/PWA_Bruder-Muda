<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Bruder Muda.
     */
    public function index(): View
    {
        $user = $this->currentUser();

        return view('user.profil.index', [
            'user' => $user,
            'photoUrl' => $this->profilePhotoUrl($user),
        ]);
    }

    /**
     * Formulir ubah profil akun yang sedang masuk.
     */
    public function edit(): View
    {
        $user = $this->currentUser();

        return view('user.profil.edit', [
            'user' => $user,
            'photoUrl' => $this->profilePhotoUrl($user),
        ]);
    }

    /**
     * Layani foto profil milik pengguna yang sedang masuk.
     * Berkas tidak dipublikasikan lewat symlink storage.
     */
    public function photo(Request $request): StreamedResponse
    {
        $path = $request->user()->profile_photo;
        abort_unless($this->isServableProfilePhoto($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Simpan profil milik pengguna yang sedang masuk.
     * Role, status aktif, username, dan id tidak diubah dari permintaan ini.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

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
            'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'community_location.max' => 'Komunitas maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'profile_photo.file' => 'Foto profil harus berupa berkas gambar.',
            'profile_photo.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 5 MB.',
        ]);

        $user->name = $validated['name'];

        if ($request->exists('community_location')) {
            $user->community_location = $validated['community_location'] ?? null;
        }

        if ($request->exists('email')) {
            $user->email = $validated['email'] ?? null;
        }

        $this->storeProfilePhoto($request, $user);

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

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function profilePhotoUrl(User $user): ?string
    {
        if (! $this->isServableProfilePhoto($user->profile_photo)) {
            return null;
        }

        return route('profil.foto');
    }

    /**
     * Simpan berkas baru di disk public dengan awalan profil/.
     * Berkas lama hanya dihapus bila path-nya juga berawalan profil/.
     */
    private function storeProfilePhoto(Request $request, User $user): void
    {
        if (! $request->hasFile('profile_photo')) {
            return;
        }

        $stored = $request->file('profile_photo')->store('profil', 'public');

        if (! is_string($stored) || ! $this->isProfilePhotoPath($stored)) {
            throw ValidationException::withMessages([
                'profile_photo' => 'Foto profil gagal disimpan.',
            ]);
        }

        $previous = $user->profile_photo;
        $user->profile_photo = $stored;

        if (is_string($previous) && $previous !== $stored && $this->isProfilePhotoPath($previous)) {
            Storage::disk('public')->delete($previous);
        }
    }

    private function isServableProfilePhoto(mixed $path): bool
    {
        return $this->isProfilePhotoPath($path) && Storage::disk('public')->exists($path);
    }

    private function isProfilePhotoPath(mixed $path): bool
    {
        return is_string($path)
            && str_starts_with($path, 'profil/')
            && $path !== 'profil/'
            && ! str_contains($path, '..')
            && ! str_contains($path, '\\')
            && ! str_ends_with($path, '/');
    }
}
