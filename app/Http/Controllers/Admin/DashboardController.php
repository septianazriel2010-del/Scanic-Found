<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ItemReport;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Statistik ringkas untuk admin dashboard. */
    public function index(): View
    {
        $stats = [
            'total_reports' => ItemReport::count(),
            'open_reports' => ItemReport::where('status', ItemReport::STATUS_OPEN)->count(),
            'lost_reports' => ItemReport::where('type', ItemReport::TYPE_LOST)->count(),
            'found_reports' => ItemReport::where('type', ItemReport::TYPE_FOUND)->count(),
            'pending_claims' => Claim::where('status', Claim::STATUS_PENDING)->count(),
            'returned_reports' => ItemReport::where('status', ItemReport::STATUS_RETURNED)->count(),
            'total_users' => User::count(),
        ];

        $recentReports = ItemReport::with('user')->latest()->limit(5)->get();
        $pendingClaims = Claim::with(['itemReport', 'claimant'])
            ->where('status', Claim::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentReports', 'pendingClaims'));
    }
}
