<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Dashboard Stats API
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Folders API
    Route::apiResource('folders', FolderController::class);

    // Files API
    Route::apiResource('files', FileController::class);
    Route::get('/files/{file}/download', [FileController::class, 'download']);
    Route::get('/files/{file}/preview', [FileController::class, 'preview']);

    // Departments API
    Route::apiResource('departments', DepartmentController::class);

    // Activity Logs API
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
});
