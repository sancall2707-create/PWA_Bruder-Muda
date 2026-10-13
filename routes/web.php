<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AgendaController;
use App\Http\Controllers\User\CalendarController;
use App\Http\Controllers\User\JournalController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ProvincialMessageController;
use App\Http\Controllers\User\SpiritualGuidanceController;
use App\Http\Controllers\User\WawanhatiController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard jika login, atau ke halaman login jika guest
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return match ($user->role) {
            'super_admin' => redirect('/super-admin'),
            'admin' => redirect('/admin'),
            default => redirect('/dashboard'),
        };
    }

    return redirect()->route('login');
});

// Guest Routes (hanya dapat diakses sebelum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Authenticated Routes (harus login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Halaman User / Bruder Muda (Dapat diakses oleh user, admin, super_admin)
    Route::middleware('role:user,admin,super_admin')->group(function () {
        // 2. Sapaan Provinsial
        Route::get('/sapaan-provinsial', [ProvincialMessageController::class, 'index'])->name('sapaan-provinsial');

        // 3. Dashboard / Menu Utama Bruder Muda
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');

        // 4 & 5. Bimbingan Rohani & Input Laporan
        Route::get('/bimbingan-rohani', [SpiritualGuidanceController::class, 'index'])->name('bimbingan.index');
        Route::get('/bimbingan-rohani/tambah', [SpiritualGuidanceController::class, 'create'])->name('bimbingan.create');
        Route::post('/bimbingan-rohani', [SpiritualGuidanceController::class, 'store'])->name('bimbingan.store');

        // 6 & 7. Wawanhati & Input Laporan Wawanhati
        Route::get('/wawanhati', [WawanhatiController::class, 'index'])->name('wawanhati.index');
        Route::get('/wawanhati/tambah', [WawanhatiController::class, 'create'])->name('wawanhati.create');
        Route::post('/wawanhati', [WawanhatiController::class, 'store'])->name('wawanhati.store');

        // 8, 9, 10, 11. Agenda Pribadi & Input Jadwal
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/bimbingan/tambah', [AgendaController::class, 'createBimbingan'])->name('agenda.bimbingan.create');
        Route::get('/agenda/wawanhati/tambah', [AgendaController::class, 'createWawanhati'])->name('agenda.wawanhati.create');
        Route::get('/agenda/lainnya/tambah', [AgendaController::class, 'createLainnya'])->name('agenda.lainnya.create');
        Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');

        // 12 & 13. Jurnal Pengembangan Diri & Input Jurnal
        Route::get('/jurnal', [JournalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/tambah', [JournalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal', [JournalController::class, 'store'])->name('jurnal.store');

        // 14. Kalender Kegiatan
        Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender');

        // 15. Profil User
        Route::get('/profil', [ProfileController::class, 'index'])->name('profil');
    });

    // Dashboard Admin (admin, super_admin)
    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin,super_admin')
        ->name('admin.dashboard');

    // Dashboard Super Admin (super_admin saja)
    Route::get('/super-admin', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('superadmin.dashboard');

    // Fase 2 Routes Super Admin
    Route::middleware('role:super_admin')->prefix('super-admin')->name('superadmin.')->group(function () {
        Route::get('/fase-1-check', function () {
            $users = \App\Models\User::orderBy('role')->orderBy('name')->get();
            return view('dashboard.fase-1-check', compact('users'));
        })->name('fase1-check');

        Route::get('/fase-2-check', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('fase2-check');

        // Manajemen User / Bruder Muda
        Route::get('/users', [App\Http\Controllers\SuperAdmin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\SuperAdmin\UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\SuperAdmin\UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/track-record', [App\Http\Controllers\SuperAdmin\UserManagementController::class, 'trackRecord'])->name('users.track-record');

        // Monitoring Laporan & Agenda
        Route::get('/monitoring', [App\Http\Controllers\SuperAdmin\MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/export', [App\Http\Controllers\SuperAdmin\MonitoringController::class, 'export'])->name('monitoring.export');

        // Sapaan & Publikasi
        Route::get('/sapaan', [App\Http\Controllers\SuperAdmin\ProvincialMessageManagementController::class, 'index'])->name('sapaan.index');
        Route::post('/sapaan', [App\Http\Controllers\SuperAdmin\ProvincialMessageManagementController::class, 'store'])->name('sapaan.store');
    });
});
