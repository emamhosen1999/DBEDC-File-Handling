<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-hidden">
        <!-- Nebula Background -->
        <div class="nebula-bg"></div>
        <div class="nebula-stars"></div>

        <div class="min-h-screen relative z-10 flex flex-col items-center justify-center p-6 lg:p-8">
            <div class="glass glass-heavy p-8 lg:p-12 w-full max-w-3xl tilt-card scene text-center">
                <div class="float-bob">
                    <x-application-logo class="w-32 h-32 mx-auto text-white holo-text mb-8" />
                </div>
                
                <h1 class="heading-display holo-text mb-4">DBEDC File Tracker</h1>
                <p class="text-xl text-[var(--color-fg-subtle)] mb-10 eyebrow">Next-Generation Departmental Workflow Automation</p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="ng-btn ng-btn-primary ng-btn-lg">
                                Enter Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="ng-btn ng-btn-primary ng-btn-lg w-full sm:w-auto">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ng-btn ng-btn-secondary ng-btn-lg w-full sm:w-auto">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <div class="mt-16 flex justify-center gap-4">
                    <span class="ng-badge ng-badge-plasma">Version {{ app()->version() }}</span>
                    <span class="ng-badge ng-badge-aurora">Nebula UI Enabled</span>
                </div>
            </div>
        </div>
    </body>
</html>
