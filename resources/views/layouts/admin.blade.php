<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') — {{ config('app.name', 'Laravel') }}</title>

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
<body class="antialiased bg-dark-950 text-dark-200">
    <div class="flex h-screen overflow-hidden">
        @include('partials.admin.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('partials.admin.header')

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-neon-500/10 border border-neon-500/20 text-neon-400 flex items-center gap-3">
                        <i class="ri-checkbox-circle-line text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="ri-error-warning-line text-lg"></i>
                            <span class="text-sm font-bold">Please correct the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
