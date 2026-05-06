<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#667eea">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Service Worker Registration -->
        @if(auth()->check())
            <script>
                if ('serviceWorker' in navigator) {
                    window.addEventListener('load', () => {
                        navigator.serviceWorker.register('/sw.js')
                            .then(registration => {
                                console.log('ServiceWorker registered: ', registration);
                            })
                            .catch(registrationError => {
                                console.log('ServiceWorker registration failed: ', registrationError);
                            });
                    });
                }
            </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <!-- Nebula Background -->
        <div class="nebula-bg"></div>
        <div class="nebula-stars"></div>

        <div class="min-h-screen relative z-10">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/10 backdrop-blur-md border border-white/20 shadow-glass-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
