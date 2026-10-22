<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SuperAdminOverviewService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function __construct(
        protected SuperAdminOverviewService $overviewService
    ) {}

    /**
     * Tampilkan daftar bruder muda & pencarian.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $users = $this->overviewService
            ->bruderDirectory($search === '' ? null : $search)
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.users.index', compact('users', 'search'));
    }

    /**
     * Form tambah akun bruder muda baru.
     */
    public function create(): View
    {
        return view('super-admin.users.create');
    }

    /**
     * Simpan akun bruder muda baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['nullable', 'string', 'in:user,admin,admin_ketua_tppbm'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'community_location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'parents_name' => ['nullable', 'string', 'max:255'],
            'last_education' => ['nullable', 'string', 'max:255'],
            'entry_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = new User;
        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = $validated['email'] ?? null;
        $user->password = $validated['password'];
        $user->role = $validated['role'] ?? 'user';
        $user->birth_place = $validated['birth_place'] ?? null;
        $user->community_location = $validated['community_location'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->parents_name = $validated['parents_name'] ?? null;
        $user->last_education = $validated['last_education'] ?? null;
        $user->entry_year = $validated['entry_year'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->is_active = true;
        $user->save();

        $roleLabel = match ($user->role) {
            'admin_ketua_tppbm' => 'Admin Ketua TPPBM',
            'admin' => 'Admin TPPBM',
            default => 'Bruder Muda',
        };

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun {$roleLabel} {$user->name} berhasil ditambahkan.");
    }

    /**
     * Form kelola email, peran, dan biodata satu akun bruder atau admin.
     */
    public function editAccount(User $user): View
    {
        return view('super-admin.users.account', compact('user'));
    }

    /**
     * Perbarui email, peran, dan biodata akun.
     */
    public function updateAccount(Request $request, User $user): RedirectResponse
    {

        $validated = $request->validate([
            'email' => [
                'nullable',
                'email',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) use ($user): void {
                    if (! is_string($value) || trim($value) === '') {
                        return;
                    }

                    $taken = User::withTrashed()
                        ->where('id', '!=', $user->id)
                        ->whereRaw('lower(email) = ?', [mb_strtolower(trim($value))])
                        ->exists();

                    if ($taken) {
                        $fail('Email ini sudah dipakai akun lain.');
                    }
                },
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $password = $validated['password'] ?? null;
        $confirmation = $request->input('password_confirmation');

        if (blank($password) && filled($confirmation)) {
            throw ValidationException::withMessages([
                'password' => 'Isi kata sandi baru beserta konfirmasinya, atau kosongkan keduanya.',
            ]);
        }

        if ($request->has('role') && in_array($request->input('role'), ['user', 'admin', 'admin_ketua_tppbm'], true)) {
            $user->role = $request->input('role');
        }

        $email = $validated['email'] ?? null;
        $user->email = is_string($email) && trim($email) !== '' ? trim($email) : null;

        if (filled($password)) {
            $user->password = $password;
        }

        // Perbarui biodata lengkap jika dikirimkan oleh Super Admin
        if ($request->boolean('update_biodata') || $request->has('family_data') || $request->has('education_data')) {
            if ($request->filled('name')) {
                $user->name = $request->input('name');
            }
            if ($request->exists('birth_place')) {
                $user->birth_place = $request->input('birth_place');
            }
            if ($request->exists('birth_date')) {
                $user->birth_date = $request->input('birth_date');
            }
            if ($request->exists('phone')) {
                $user->phone = $request->input('phone');
            }
            if ($request->exists('community_location')) {
                $user->community_location = $request->input('community_location');
            }
            if ($request->exists('bio')) {
                $user->bio = $request->input('bio');
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
        }

        $user->save();

        $message = filled($password)
            ? "Akun {$user->name} berhasil diperbarui (termasuk kata sandi baru)."
            : "Data akun {$user->name} berhasil diperbarui.";

        return redirect()->route('superadmin.users.index')
            ->with('success', $message);
    }

    /**
     * Track Record Individual Bruder Muda.
     */
    public function trackRecord(User $user): View
    {
        abort_unless($user->role === 'user', 404);

        $user->loadCount([
            'spiritualGuidanceReports',
            'wawanhatiReports',
            'selfDevelopmentJournals',
            'personalAgendas',
            'bruderNotes',
        ]);

        $recentBimbingan = $user->spiritualGuidanceReports()->latest('meeting_at')->limit(5)->get();
        $recentWawanhati = $user->wawanhatiReports()->latest('meeting_at')->limit(5)->get();
        $recentJournals = $user->selfDevelopmentJournals()->latest('activity_date')->limit(5)->get();
        $notes = $user->bruderNotes()->with('author')->latest()->get();
        $messages = $user->directMessagesReceived()->with('sender')->latest()->limit(5)->get();

        return view('super-admin.users.track-record', compact(
            'user',
            'recentBimbingan',
            'recentWawanhati',
            'recentJournals',
            'notes',
            'messages',
        ));
    }

    /**
     * Aktifkan atau nonaktifkan akun pengguna (bruder muda maupun admin).
     */
    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403);

        $user->is_active = ! $user->is_active;
        $user->save();

        $label = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun {$user->name} berhasil {$label}.");
    }

    /**
     * Hapus akun pengguna tanpa menghapus riwayat laporannya.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403);

        $name = $user->name;
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun {$name} dihapus dari daftar.");
    }
}
