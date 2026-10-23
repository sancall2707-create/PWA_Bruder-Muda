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
            'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'community_location.max' => 'Komunitas maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'profile_photo.file' => 'Foto profil harus berupa berkas gambar.',
            'profile_photo.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 10 MB.',
        ]);

        $user->name = $validated['name'];

        if ($request->exists('community_location')) {
            $user->community_location = $validated['community_location'] ?? null;
        }

        if ($request->exists('email')) {
            $user->email = $validated['email'] ?? null;
        }

        if ($request->exists('phone')) {
            $user->phone = $request->input('phone');
        }

        if ($request->exists('birth_place')) {
            $user->birth_place = $request->input('birth_place');
        }

        if ($request->exists('birth_date')) {
            $user->birth_date = $request->input('birth_date');
        }

        if ($request->has('family_data')) {
            $fam = $request->input('family_data');
            $user->family_data = is_array($fam) ? $fam : [];
            $father = $fam['father_name'] ?? '';
            $mother = $fam['mother_name'] ?? '';
            if ($father || $mother) {
                $user->parents_name = trim(($father ? "Bpk. {$father}" : '') . ($father && $mother ? ' & ' : '') . ($mother ? "Ibu {$mother}" : ''));
            }
        }

        if ($request->has('education_data')) {
            $edu = $request->input('education_data');
            $user->education_data = is_array($edu) ? $edu : [];
            if (! empty($edu['s2_name'])) {
                $user->last_education = 'S2 ' . ($edu['s2_major'] ?? '');
            } elseif (! empty($edu['s1_name'])) {
                $user->last_education = 'S1 ' . ($edu['s1_major'] ?? '');
            } elseif (! empty($edu['sma_name'])) {
                $user->last_education = 'SMA/SMK ' . ($edu['sma_name'] ?? '');
            }
        }

        if ($request->has('formation_data')) {
            $form = $request->input('formation_data');
            $user->formation_data = is_array($form) ? $form : [];
            if (! empty($form['postulat_in'])) {
                $user->entry_year = (int) $form['postulat_in'];
            }
        }

        if ($request->has('assignment_data')) {
            $asg = $request->input('assignment_data');
            $user->assignment_data = is_array($asg) ? $asg : [];
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

    /**
     * Tampilkan catatan pembinaan dari TPPBM untuk bruder muda.
     */
    public function notes(): View
    {
        $user = auth()->user();
        $notes = $user->bruderNotes()->with('author')->latest()->get();

        return view('user.profil.catatan', compact('user', 'notes'));
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
