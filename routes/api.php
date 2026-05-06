<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LetterController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StakeholderController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Letters
    Route::prefix('letters')->group(function () {
        Route::get('/', [LetterController::class, 'index']);
        Route::get('/{id}', [LetterController::class, 'show']);
        Route::post('/', [LetterController::class, 'store']);
        Route::post('/bulk', [LetterController::class, 'bulkStore']);
        Route::put('/{id}', [LetterController::class, 'update']);
        Route::patch('/{id}', [LetterController::class, 'update']);
        Route::patch('/bulk', [LetterController::class, 'bulkUpdate']);
        Route::delete('/{id}', [LetterController::class, 'destroy']);
        Route::delete('/bulk', [LetterController::class, 'bulkDestroy']);
        Route::get('/export/{format}', [LetterController::class, 'export']);
        Route::get('/calendar', [LetterController::class, 'calendar']);
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::post('/', [TaskController::class, 'store']);
        Route::patch('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
    });

    // Departments
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::get('/tree', [DepartmentController::class, 'tree']);
        Route::get('/stats', [DepartmentController::class, 'stats']);
        Route::get('/{id}', [DepartmentController::class, 'show']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::put('/{id}', [DepartmentController::class, 'update']);
        Route::patch('/{id}', [DepartmentController::class, 'update']);
        Route::delete('/{id}', [DepartmentController::class, 'destroy']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Stakeholders
    Route::prefix('stakeholders')->group(function () {
        Route::get('/', [StakeholderController::class, 'index']);
        Route::get('/{id}', [StakeholderController::class, 'show']);
        Route::post('/', [StakeholderController::class, 'store']);
        Route::put('/{id}', [StakeholderController::class, 'update']);
        Route::patch('/{id}', [StakeholderController::class, 'update']);
        Route::delete('/{id}', [StakeholderController::class, 'destroy']);
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::get('/{key}', [SettingController::class, 'show']);
        Route::post('/', [SettingController::class, 'store']);
        Route::put('/{key}', [SettingController::class, 'update']);
        Route::patch('/{key}', [SettingController::class, 'update']);
        Route::delete('/{key}', [SettingController::class, 'destroy']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Activities
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
        Route::get('/{id}', [ActivityController::class, 'show']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
    });

    // Search
    Route::get('/search', [SearchController::class, 'index']);
});
