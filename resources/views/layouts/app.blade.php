<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased bg-dark-950 text-dark-200 selection:bg-neon-500/20 selection:text-neon-400">
    <div class="min-h-screen flex flex-col relative overflow-hidden">
        <!-- Background Grid Decoration -->
        <div class="absolute inset-0 bg-cyber-grid bg-[length:32px_32px] pointer-events-none opacity-40"></div>
        
        @include('partials.header')

        <main class="flex-grow relative z-10">
            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    @stack('scripts')
</body>
</html>
