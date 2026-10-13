<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SuperAdminOverviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'community_location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
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
        $user->role = 'user';
        $user->community_location = $validated['community_location'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->parents_name = $validated['parents_name'] ?? null;
        $user->last_education = $validated['last_education'] ?? null;
        $user->entry_year = $validated['entry_year'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->is_active = true;
        $user->save();

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun Bruder {$user->name} berhasil ditambahkan.");
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
        ]);

        $recentBimbingan = $user->spiritualGuidanceReports()->latest('meeting_at')->limit(5)->get();
        $recentWawanhati = $user->wawanhatiReports()->latest('meeting_at')->limit(5)->get();
        $recentJournals = $user->selfDevelopmentJournals()->latest('activity_date')->limit(5)->get();

        return view('super-admin.users.track-record', compact(
            'user',
            'recentBimbingan',
            'recentWawanhati',
            'recentJournals',
        ));
    }

    /**
     * Aktifkan atau nonaktifkan akun bruder muda.
     */
    public function updateStatus(User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);

        $user->is_active = ! $user->is_active;
        $user->save();

        $label = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun {$user->name} berhasil {$label}.");
    }

    /**
     * Hapus akun bruder muda tanpa menghapus riwayat laporannya.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);
        abort_if($user->is($request->user()), 403);

        $name = $user->name;
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun {$name} dihapus dari daftar bruder muda.");
    }
}
