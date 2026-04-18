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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased bg-dark-950 text-dark-200 selection:bg-neon-500/20 selection:text-neon-400">
    <div class="flex h-screen overflow-hidden">
        @include('partials.admin.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('partials.admin.header')

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <div class="max-w-7xl mx-auto space-y-8">
                    {{-- Page Header --}}
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div class="space-y-2">
                            <h1 class="text-3xl font-black text-dark-100 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-dark-400 font-medium">@yield('page-subtitle', 'Welcome to your management suite.')</p>
                        </div>
                        @yield('page-actions')
                    </div>

                    {{-- Notifications --}}
                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-neon-500/10 border border-neon-500/20 text-neon-400 text-sm font-bold flex items-center gap-3 animate-fade-in">
                            <i class="ri-checkbox-circle-line text-xl"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400">
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
                </div>

                {{-- Footer --}}
                <footer class="mt-20 py-6 border-t border-dark-600/50 text-center">
                    <p class="text-xs text-dark-500 font-bold uppercase tracking-[0.2em]">
                        &copy; {{ date('Y') }} Patrick CMS — System Operational
                    </p>
                </footer>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
