<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Tampilkan daftar bruder muda & pencarian.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('community_location', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10);

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
            'entry_year' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = $validated['email'] ?? null;
        $user->password = Hash::make($validated['password']);
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
        $user->load([
            'spiritualGuidanceReports' => fn($q) => $q->latest('meeting_at')->limit(5),
            'wawanhatiReports' => fn($q) => $q->latest('meeting_at')->limit(5),
            'selfDevelopmentJournals' => fn($q) => $q->latest('activity_date')->limit(5),
            'personalAgendas' => fn($q) => $q->latest('start_at')->limit(5),
        ]);

        return view('super-admin.users.track-record', compact('user'));
    }
}
