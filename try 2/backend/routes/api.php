<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/registration-mode', [AuthController::class, 'checkRegistrationMode']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    
    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::get('/activity-logs/stats', [ActivityLogController::class, 'stats']);
    
    // Export
    Route::get('/export/requests', [ExportController::class, 'exportRequests']);
    Route::get('/export/summary', [ExportController::class, 'exportRequestsSummary']);

    // Request management
    Route::apiResource('requests', RequestController::class)->parameters(['requests' => 'requestModel']);

    // Admin only routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        // Export users
        Route::get('/export/users', [ExportController::class, 'exportUsers']);

        // Request management
        Route::get('/requests', [AdminRequestController::class, 'index']);
        Route::post('/requests/{requestModel}/approve', [AdminRequestController::class, 'approve']);
        Route::post('/requests/{requestModel}/reject', [AdminRequestController::class, 'reject']);
        Route::put('/requests/{requestModel}/note', [AdminRequestController::class, 'updateNote']);

        // System settings
        Route::get('/settings', [SystemSettingController::class, 'index']);
        Route::put('/settings', [SystemSettingController::class, 'update']);
        Route::post('/settings/reset', [SystemSettingController::class, 'reset']);
        
        // Activity Logs (Admin)
        Route::get('/activity-logs', [ActivityLogController::class, 'all']);
        Route::get('/activity-logs/stats', [ActivityLogController::class, 'stats']);
    });
});

