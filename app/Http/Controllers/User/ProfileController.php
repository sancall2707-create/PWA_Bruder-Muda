<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

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
}
