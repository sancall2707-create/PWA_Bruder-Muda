<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdminOverviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected SuperAdminOverviewService $overviewService
    ) {}

    /**
     * Tampilkan halaman utama dashboard Super Admin.
     */
    public function index(Request $request): View
    {
        $stats = $this->overviewService->monthlySummary();
        $bruders = $this->overviewService->bruderCards();

        return view('super-admin.dashboard', compact('stats', 'bruders'));
    }
}
