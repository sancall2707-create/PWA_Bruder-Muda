<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard User.
     * Dapat diakses oleh role: user, admin, super_admin.
     */
    public function user(): View
    {
        return view('dashboard.user');
    }

    /**
     * Tampilkan halaman Dashboard Admin.
     * Dapat diakses oleh role: admin, super_admin.
     */
    public function admin(): View
    {
        return view('dashboard.admin');
    }

    /**
     * Tampilkan halaman Dashboard Super Admin.
     * Dapat diakses oleh role: super_admin.
     */
    public function superAdmin(): View
    {
        return view('dashboard.super-admin');
    }
}
