<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installation - DBEDC File Tracker')</title>
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="antialiased min-h-screen bg-slate-950 text-gray-100">
    <!-- Dynamic Nebula Background -->
    <div class="nebula-bg">
        <div class="nebula-stars"></div>
    </div>
    
    <div class="relative z-10 flex flex-col sm:justify-center items-center min-h-screen pt-6 sm:pt-0">
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>
