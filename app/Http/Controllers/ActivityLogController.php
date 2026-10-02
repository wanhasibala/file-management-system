<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display activity log history.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorize('create', ActivityLog::class); // admin only

        $logs = ActivityLog::with('user')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'logs' => $logs,
            ]);
        }

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
        ]);
    }
}
