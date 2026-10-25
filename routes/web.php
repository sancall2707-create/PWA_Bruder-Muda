<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\BruderNoteController;
use App\Http\Controllers\SuperAdmin\DirectMessageController;
use App\Http\Controllers\SuperAdmin\MonitoringController;
use App\Http\Controllers\SuperAdmin\ProvincialMessageManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\User\AgendaController;
use App\Http\Controllers\User\CalendarController;
use App\Http\Controllers\User\JournalController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ProvincialMessageController;
use App\Http\Controllers\User\SpiritualGuidanceController;
use App\Http\Controllers\User\UserDirectMessageController;
use App\Http\Controllers\User\WawanhatiController;
use App\Models\User;
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
        Route::get('/sapaan-provinsial/foto/{message}', [ProvincialMessageController::class, 'photo'])->name('sapaan-provinsial.foto');

        // 3. Dashboard / Menu Utama Bruder Muda
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');

        // 4 & 5. Bimbingan Rohani & Input Laporan
        Route::get('/bimbingan-rohani', [SpiritualGuidanceController::class, 'index'])->name('bimbingan.index');
        Route::get('/bimbingan-rohani/tambah', [SpiritualGuidanceController::class, 'create'])->name('bimbingan.create');
        Route::post('/bimbingan-rohani', [SpiritualGuidanceController::class, 'store'])->name('bimbingan.store');
        Route::get('/bimbingan-rohani/{report}/edit', [SpiritualGuidanceController::class, 'edit'])->name('bimbingan.edit');
        Route::put('/bimbingan-rohani/{report}', [SpiritualGuidanceController::class, 'update'])->name('bimbingan.update');
        Route::delete('/bimbingan-rohani/{report}', [SpiritualGuidanceController::class, 'destroy'])->name('bimbingan.destroy');
        Route::get('/bimbingan-rohani/{report}/bukti', [SpiritualGuidanceController::class, 'proof'])->name('bimbingan.proof');

        // 6 & 7. Wawanhati & Input Laporan Wawanhati
        Route::get('/wawanhati', [WawanhatiController::class, 'index'])->name('wawanhati.index');
        Route::get('/wawanhati/tambah', [WawanhatiController::class, 'create'])->name('wawanhati.create');
        Route::post('/wawanhati', [WawanhatiController::class, 'store'])->name('wawanhati.store');
        Route::get('/wawanhati/{report}/edit', [WawanhatiController::class, 'edit'])->name('wawanhati.edit');
        Route::put('/wawanhati/{report}', [WawanhatiController::class, 'update'])->name('wawanhati.update');
        Route::delete('/wawanhati/{report}', [WawanhatiController::class, 'destroy'])->name('wawanhati.destroy');
        Route::get('/wawanhati/{report}/bukti', [WawanhatiController::class, 'proof'])->name('wawanhati.proof');

        // 8, 9, 10, 11. Agenda Pribadi & Input Jadwal
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/bimbingan/tambah', [AgendaController::class, 'createBimbingan'])->name('agenda.bimbingan.create');
        Route::get('/agenda/wawanhati/tambah', [AgendaController::class, 'createWawanhati'])->name('agenda.wawanhati.create');
        Route::get('/agenda/lainnya/tambah', [AgendaController::class, 'createLainnya'])->name('agenda.lainnya.create');
        Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
        Route::get('/agenda/{type}/{schedule}/edit', [AgendaController::class, 'edit'])
            ->whereIn('type', ['bimbingan', 'wawanhati', 'lainnya'])
            ->name('agenda.edit');
        Route::put('/agenda/{type}/{schedule}', [AgendaController::class, 'update'])
            ->whereIn('type', ['bimbingan', 'wawanhati', 'lainnya'])
            ->name('agenda.update');
        Route::delete('/agenda/{type}/{schedule}', [AgendaController::class, 'destroy'])
            ->whereIn('type', ['bimbingan', 'wawanhati', 'lainnya'])
            ->name('agenda.destroy');

        // 12 & 13. Jurnal Pengembangan Diri & Input Jurnal
        Route::get('/jurnal', [JournalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/tambah', [JournalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal', [JournalController::class, 'store'])->name('jurnal.store');
        Route::delete('/jurnal/bulk-delete', [JournalController::class, 'destroyBulk'])->name('jurnal.destroyBulk');
        Route::get('/jurnal/{journal}/edit', [JournalController::class, 'edit'])->name('jurnal.edit');
        Route::put('/jurnal/{journal}', [JournalController::class, 'update'])->name('jurnal.update');
        Route::delete('/jurnal/{journal}', [JournalController::class, 'destroy'])->name('jurnal.destroy');

        // 14. Kalender Kegiatan
        Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender');

        // 15. Profil User
        Route::get('/profil', [ProfileController::class, 'index'])->name('profil');
        Route::get('/profil/foto', [ProfileController::class, 'photo'])->name('profil.foto');
        Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
        Route::post('/profil', [ProfileController::class, 'update'])->name('profil.update');
        Route::get('/profil/kata-sandi', [ProfileController::class, 'editPassword'])->name('profil.kata-sandi');
        Route::post('/profil/kata-sandi', [ProfileController::class, 'updatePassword'])->name('profil.kata-sandi.update');
        Route::get('/profil/catatan', [ProfileController::class, 'notes'])->name('profil.catatan');

        // 16. Pesan Khusus Privat (Satu Arah) dari Pimpinan / TPPBM
        Route::get('/pesan-khusus', [UserDirectMessageController::class, 'index'])->name('pesan.index');
        Route::get('/pesan-khusus/{message}', [UserDirectMessageController::class, 'show'])->name('pesan.show');
    });

    // Dashboard Admin (admin, super_admin)
    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin,super_admin')
        ->name('admin.dashboard');

    // Dashboard Super Admin (super_admin saja)
    Route::get('/super-admin', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('superadmin.dashboard');

    // Fase 2 Routes - Kolaborasi TPPBM & Super Admin
    Route::middleware('role:admin,super_admin')->prefix('super-admin')->name('superadmin.')->group(function () {
        // Profil & Edit Akun Admin / Super Admin
        Route::get('/profil', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'index'])->name('profil');
        Route::post('/profil', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profil.update');
        Route::post('/profil/kata-sandi', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'updatePassword'])->name('profil.password');

        // Direktori & Track Record Bruder
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/foto', [UserManagementController::class, 'photo'])->name('users.photo');
        Route::get('/users/{user}/track-record', [UserManagementController::class, 'trackRecord'])->name('users.track-record');

        // Catatan Pembinaan Bruder (Poin 5 & Fitur Tarik/Edit Catatan)
        Route::get('/users/{user}/catatan/tambah', [BruderNoteController::class, 'create'])->name('users.notes.create');
        Route::post('/users/{user}/catatan', [BruderNoteController::class, 'store'])->name('users.notes.store');
        Route::get('/users/{user}/catatan/{note}/edit', [BruderNoteController::class, 'edit'])->name('users.notes.edit');
        Route::put('/users/{user}/catatan/{note}', [BruderNoteController::class, 'update'])->name('users.notes.update');
        Route::delete('/users/{user}/catatan/{note}', [BruderNoteController::class, 'destroy'])->name('users.notes.destroy');

        // Kirim Pesan Khusus Satu Arah (Poin 2)
        Route::get('/users/{user}/pesan', [DirectMessageController::class, 'create'])->name('users.messages.create');
        Route::post('/users/{user}/pesan', [DirectMessageController::class, 'store'])->name('users.messages.store');
        Route::delete('/users/{user}/pesan/{message}', [DirectMessageController::class, 'destroy'])->name('users.messages.destroy');

        // Monitoring Laporan & Raport
        Route::get('/monitoring/raport', [MonitoringController::class, 'raport'])->name('monitoring.raport');
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::delete('/monitoring/laporan/bulk', [MonitoringController::class, 'destroyReportBulk'])->name('monitoring.laporan.destroyBulk');
        Route::delete('/monitoring/laporan/{type}/{id}', [MonitoringController::class, 'destroyReportSingle'])->name('monitoring.laporan.destroySingle');
        Route::get('/monitoring/agenda/tambah', [MonitoringController::class, 'agendaCreate'])->name('monitoring.agenda.create');
        Route::post('/monitoring/agenda', [MonitoringController::class, 'agendaStore'])->name('monitoring.agenda.store');
        Route::get('/monitoring/agenda', [MonitoringController::class, 'agenda'])->name('monitoring.agenda');
        Route::delete('/monitoring/agenda/{category}/{id}', [MonitoringController::class, 'destroyAgendaSingle'])->name('monitoring.agenda.destroySingle');
        Route::get('/monitoring/export', [MonitoringController::class, 'export'])->name('monitoring.export');
        Route::get('/monitoring/jurnal', [MonitoringController::class, 'jurnalIndex'])->name('monitoring.jurnal.index');
        Route::delete('/monitoring/jurnal/bulk', [MonitoringController::class, 'jurnalDestroyBulk'])->name('monitoring.jurnal.destroyBulk');

        // Sapaan & Publikasi
        Route::get('/sapaan', [ProvincialMessageManagementController::class, 'index'])->name('sapaan.index');
        Route::post('/sapaan', [ProvincialMessageManagementController::class, 'store'])->name('sapaan.store');
        Route::get('/sapaan/{message}/edit', [ProvincialMessageManagementController::class, 'edit'])->name('sapaan.edit');
        Route::put('/sapaan/{message}', [ProvincialMessageManagementController::class, 'update'])->name('sapaan.update');
        Route::post('/sapaan/{message}/status', [ProvincialMessageManagementController::class, 'updateStatus'])->name('sapaan.status');
        Route::delete('/sapaan/{message}', [ProvincialMessageManagementController::class, 'destroy'])->name('sapaan.destroy');
    });

    // Modul Khusus Super Admin (Akses Sistem & Akun)
    Route::middleware('role:super_admin')->prefix('super-admin')->name('superadmin.')->group(function () {
        Route::get('/fase-1-check', function () {
            $users = User::orderBy('role')->orderBy('name')->get();

            return view('dashboard.fase-1-check', compact('users'));
        })->name('fase1-check');

        Route::get('/fase-2-check', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('fase2-check');

        // Aksi Akun Super Admin
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/kelola-akun', [UserManagementController::class, 'editAccount'])->name('users.account.edit');
        Route::post('/users/{user}/kelola-akun', [UserManagementController::class, 'updateAccount'])->name('users.account.update');
        Route::post('/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.status');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});
