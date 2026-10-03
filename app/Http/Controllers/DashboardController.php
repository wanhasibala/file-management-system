<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DashboardService;
use App\Services\FolderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected FolderService $folderService
    ) {}

    /**
     * Display the dashboard view with statistics and latest files.
     */
    public function index(Request $request): Response
    {
        $data = $this->dashboardService->getDashboardData();
        $departments = Department::orderBy('name')->get();
        $folderTree = $this->folderService->getFolderTree();

        return Inertia::render('Dashboard', [
            'stats' => $data['stats'],
            'latestFiles' => $data['latest_files'],
            'departmentBreakdown' => $data['department_breakdown'],
            'recentActivities' => $data['recent_activities'],
            'topDownloaded' => $data['top_downloaded'],
            'departments' => $departments,
            'folderTree' => $folderTree,
        ]);
    }
}
