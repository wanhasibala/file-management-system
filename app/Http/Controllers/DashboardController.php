<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard view with statistics and latest files.
     */
    public function index(Request $request): Response
    {
        $data = $this->dashboardService->getDashboardData();

        return Inertia::render('Dashboard', [
            'stats' => $data['stats'],
            'latestFiles' => $data['latest_files'],
            'departmentBreakdown' => $data['department_breakdown'],
            'recentActivities' => $data['recent_activities'],
            'topDownloaded' => $data['top_downloaded'],
        ]);
    }
}
