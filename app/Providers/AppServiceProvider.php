<?php

namespace App\Providers;

use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use App\Policies\LetterPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Letter::class, LetterPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        Gate::define('access-admin', function (User $user): bool {
            return in_array($user->role, ['ADMIN', 'MANAGER'], true);
        });

        Gate::define('manage-users', function (User $user): bool {
            return $user->role === 'ADMIN';
        });

        Gate::define('manage-departments', function (User $user): bool {
            return in_array($user->role, ['ADMIN', 'MANAGER'], true);
        });

        Gate::define('view-activity-log', function (User $user): bool {
            return in_array($user->role, ['ADMIN', 'MANAGER'], true);
        });
    }
}
