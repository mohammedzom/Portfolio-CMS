<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-900" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Sidebar for Mobile -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 flex">
                <div class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white transition-all duration-300">
                    <div class="absolute left-full top-0 flex justify-center pt-5 w-16">
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-white">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>
                    @include('partials.admin.sidebar')
                </div>
            </div>
        </div>

        <!-- Sidebar for Desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col lg:border-r lg:border-slate-200 lg:bg-white">
            @include('partials.admin.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="lg:pl-72">
            <!-- Header -->
            <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-slate-700 lg:hidden">
                    <i class="ri-menu-2-line text-xl"></i>
                </button>
                
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-500">Dashboard</span>
                        <i class="ri-arrow-right-s-line text-slate-400"></i>
                        <span class="text-sm font-bold text-slate-900">@yield('page-title', 'Overview')</span>
                    </div>

                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <a href="{{ route('portfolio.index') }}" target="_blank" class="text-sm font-medium text-slate-600 hover:text-indigo-600 flex items-center gap-1 transition-colors">
                            <i class="ri-external-link-line"></i>
                            View Site
                        </a>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-red-600 transition-colors">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-8 rounded-xl bg-emerald-50 p-4 border border-emerald-100 flex items-center gap-3 text-emerald-800 animate-fade-in">
                            <i class="ri-checkbox-circle-fill text-emerald-500 text-xl"></i>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
