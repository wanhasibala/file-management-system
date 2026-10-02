<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get aggregated statistics and recent data for the dashboard.
     */
    public function getDashboardData(): array
    {
        $totalFolders = Folder::count();
        $totalFiles = File::count();
        $totalDepartments = Department::count();
        $totalStorageBytes = File::sum('file_size') ?: 0;

        // 10 Latest Files
        $latestFiles = File::with(['department', 'folder', 'user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Department breakdown
        $departmentBreakdown = Department::withCount('files')
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'color' => $dept->color,
                    'files_count' => $dept->files_count,
                ];
            });

        // Recent Activity Logs
        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->limit(8)
            ->get();

        // Top downloaded files
        $topDownloaded = File::with(['department'])
            ->where('download_count', '>', 0)
            ->orderByDesc('download_count')
            ->limit(5)
            ->get();

        return [
            'stats' => [
                'total_folders' => $totalFolders,
                'total_files' => $totalFiles,
                'total_departments' => $totalDepartments,
                'total_storage_bytes' => $totalStorageBytes,
                'formatted_storage' => $this->formatBytes($totalStorageBytes),
            ],
            'latest_files' => $latestFiles,
            'department_breakdown' => $departmentBreakdown,
            'recent_activities' => $recentActivities,
            'top_downloaded' => $topDownloaded,
        ];
    }

    private function formatBytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
