<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = $this->isInstalled();

        // If accessing installation routes
        if ($request->is('install*')) {
            // If already installed, redirect to dashboard
            if ($isInstalled) {
                return redirect()->route('dashboard');
            }
            // If not installed, allow access to installation wizard
            return $next($request);
        }

        // If not accessing installation routes and app is not installed, redirect to install
        if (!$isInstalled) {
            return redirect()->route('install');
        }

        // App is installed, allow access
        return $next($request);
    }

    private function isInstalled(): bool
    {
        // Check for lock file
        $lockFile = storage_path('installed.lock');
        if (File::exists($lockFile)) {
            return true;
        }

        // Check for INSTALLED=true in .env
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);
            return str_contains($envContent, 'INSTALLED=true');
        }

        return false;
    }
}
