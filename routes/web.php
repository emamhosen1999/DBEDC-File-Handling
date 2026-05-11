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
        return redirect()->route(auth()->check() ? 'dashboard' : 'login');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Letters
        Route::get('/letters', \App\Livewire\Letters\Index::class)->name('letters.index');
        Route::get('/letters/create', \App\Livewire\Letters\Create::class)->name('letters.create');
        Route::get('/letters/{letter}', \App\Livewire\Letters\Show::class)->name('letters.show');
        Route::get('/letters/{letter}/edit', \App\Livewire\Letters\Edit::class)->name('letters.edit');
        Route::get('/letters/{letter}/download', [\App\Http\Controllers\LetterAttachmentController::class, 'download'])->name('letters.download');

        // Tasks
        Route::get('/tasks', \App\Livewire\Tasks\Index::class)->name('tasks.index');
        Route::get('/tasks/create', \App\Livewire\Tasks\Create::class)->name('tasks.create');
        Route::get('/tasks/{task}', \App\Livewire\Tasks\Show::class)->name('tasks.show');
        Route::get('/tasks/{task}/edit', \App\Livewire\Tasks\Edit::class)->name('tasks.edit');

        // Notifications
        Route::get('/notifications', \App\Livewire\Notifications\Index::class)->name('notifications.index');

        // Administration
        Route::middleware('can:access-admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', function () {
                return view('admin.dashboard');
            })->name('dashboard');
            Route::get('/users', \App\Livewire\Admin\Users\Index::class)->name('users.index');
            Route::get('/users/{user}/edit', \App\Livewire\Admin\Users\Edit::class)->name('users.edit');
            Route::get('/departments', \App\Livewire\Admin\Departments\Index::class)->name('departments.index');
            Route::get('/stakeholders', \App\Livewire\Admin\Stakeholders\Index::class)->name('stakeholders.index');
            Route::get('/activities', \App\Livewire\Admin\Activities\Index::class)->name('activities.index');
        });
    });

    // Include auth routes (also protected by installation check)
    require __DIR__.'/auth.php';
});

// OAuth Routes (outside installation check for OAuth flow)
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::get('/auth/wechat', [WeChatController::class, 'redirect'])->name('auth.wechat');
Route::get('/auth/wechat/callback', [WeChatController::class, 'callback'])->name('auth.wechat.callback');
