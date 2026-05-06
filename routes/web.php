<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\WeChatController;
use App\Http\Controllers\InstallationController;
use Illuminate\Support\Facades\Route;

// Installation route (no middleware, handled by CheckInstallation)
Route::get('/install', [InstallationController::class, 'index'])->name('install');
Route::post('/install', [InstallationController::class, 'process'])->name('install.process');

// Apply check.installation middleware to all web routes
Route::middleware('check.installation')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Letters
        Route::get('/letters', function () {
            return view('letters.index');
        })->name('letters.index');

        // Tasks
        Route::get('/tasks', function () {
            return view('tasks.index');
        })->name('tasks.index');

        // Administration
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Include auth routes (also protected by installation check)
    require __DIR__.'/auth.php';
});

// OAuth Routes (outside installation check for OAuth flow)
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::get('/auth/wechat', [WeChatController::class, 'redirect'])->name('auth.wechat');
Route::get('/auth/wechat/callback', [WeChatController::class, 'callback'])->name('auth.wechat.callback');
