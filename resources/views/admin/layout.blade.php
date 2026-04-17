<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', 'Dashboard') — Patrick CMS</title>
    <style>
        /* Sidebar transitions */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s ease; }
        #sidebar.collapsed { width: 72px; }
        #sidebar.collapsed .sidebar-label { display: none; }
        #sidebar.collapsed .sidebar-logo-text { display: none; }
        #sidebar.collapsed .sidebar-section-title { display: none; }
        #sidebar.collapsed .sidebar-badge { display: none; }

        /* Nav item active */
        .sidebar-link.active {
            background: oklch(0.66 0.17 195 / 0.12);
            border-color: oklch(0.66 0.17 195 / 0.4);
            color: oklch(0.66 0.17 195);
        }
        .sidebar-link.active .sidebar-icon { color: oklch(0.66 0.17 195); }
        .sidebar-link:not(.active):hover {
            background: oklch(0.66 0.17 195 / 0.05);
            border-color: oklch(0.66 0.17 195 / 0.15);
        }

        /* Scrollbar in sidebar */
        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: oklch(0.66 0.17 195 / 0.2); border-radius: 99px; }

        /* Mobile overlay */
        #sidebar-overlay { display: none; }
        #sidebar-overlay.show { display: block; }

        /* Stat card hover */
        .stat-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); }
    </style>
</head>
<body class="antialiased" style="background-color: oklch(0.10 0.01 255);">

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar" class="relative z-30 flex flex-col w-64 shrink-0 border-r border-dark-700 overflow-hidden" style="background: oklch(0.12 0.01 255);">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-5 h-16 border-b border-dark-700 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg gradient-neon flex items-center justify-center shrink-0 group-hover:neon-glow transition-all">
                    <i class="ri-code-s-slash-line text-dark-950 font-bold"></i>
                </div>
                <span class="sidebar-logo-text font-display font-bold text-dark-100 text-lg">Patrick<span class="text-neon-500">.cms</span></span>
            </a>
            <button id="sidebar-toggle" class="p-1.5 rounded-lg text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all duration-200 hidden lg:flex items-center justify-center">
                <i id="toggle-icon" class="ri-layout-left-line text-lg"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav id="sidebar-nav" class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <p class="sidebar-section-title text-dark-600 text-xs font-semibold uppercase tracking-widest px-3 pb-2">Main</p>

            @php
            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => 'ri-dashboard-3-line', 'label' => 'Dashboard', 'badge' => null],
                ['route' => 'admin.projects',  'icon' => 'ri-folder-4-line',    'label' => 'Projects',  'badge' => '6'],
                ['route' => 'admin.skills',    'icon' => 'ri-bar-chart-2-line', 'label' => 'Skills',    'badge' => null],
                ['route' => 'admin.services',  'icon' => 'ri-service-line',     'label' => 'Services',  'badge' => null],
            ];
            $bottomItems = [
                ['route' => 'admin.messages',  'icon' => 'ri-mail-line',        'label' => 'Messages',  'badge' => '3'],
                ['route' => 'admin.settings',  'icon' => 'ri-settings-3-line',  'label' => 'Settings',  'badge' => null],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent transition-all duration-200 {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                <i class="sidebar-icon {{ $item['icon'] }} text-lg text-dark-500 group-hover:text-neon-500 shrink-0 transition-colors"></i>
                <span class="sidebar-label text-dark-300 group-hover:text-dark-100 text-sm font-medium transition-colors flex-1">{{ $item['label'] }}</span>
                @if($item['badge'])
                <span class="sidebar-badge text-xs px-2 py-0.5 rounded-full bg-neon-500/15 text-neon-400 font-semibold">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach

            <div class="pt-4 pb-2">
                <p class="sidebar-section-title text-dark-600 text-xs font-semibold uppercase tracking-widest px-3 pb-2">System</p>
                @foreach($bottomItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent transition-all duration-200 {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <i class="sidebar-icon {{ $item['icon'] }} text-lg text-dark-500 group-hover:text-neon-500 shrink-0 transition-colors"></i>
                    <span class="sidebar-label text-dark-300 group-hover:text-dark-100 text-sm font-medium transition-colors flex-1">{{ $item['label'] }}</span>
                    @if($item['badge'])
                    <span class="sidebar-badge text-xs px-2 py-0.5 rounded-full bg-neon-500/15 text-neon-400 font-semibold">{{ $item['badge'] }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </nav>

        {{-- User profile / logout --}}
        <div class="px-3 pb-4 shrink-0 border-t border-dark-700 pt-3">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-dark-800 transition-all cursor-pointer group text-left">
                    <div class="w-8 h-8 rounded-full gradient-neon flex items-center justify-center text-dark-950 font-bold text-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="sidebar-label flex-1 min-w-0">
                        <p class="text-dark-100 text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-dark-500 text-xs truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <i class="sidebar-label ri-logout-box-r-line text-dark-500 group-hover:text-neon-500 transition-colors"></i>
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-dark-950/70 backdrop-blur-sm z-20 lg:hidden" onclick="closeMobileSidebar()"></div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 flex items-center justify-between px-6 border-b border-dark-700 shrink-0" style="background: oklch(0.12 0.01 255);">
            <div class="flex items-center gap-4">
                {{-- Mobile hamburger --}}
                <button id="mobile-menu-btn" class="lg:hidden p-1.5 rounded-xl text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all">
                    <i class="ri-menu-2-line text-xl"></i>
                </button>

                {{-- Page title --}}
                <div>
                    <h1 class="font-display font-bold text-dark-100 text-lg">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-dark-500 text-xs">@yield('page-subtitle', 'Welcome back, Patrick!')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="hidden sm:flex items-center gap-2 glass rounded-xl px-3 py-2 w-48 lg:w-64 border border-dark-700 focus-within:border-neon-500/40 transition-colors">
                    <i class="ri-search-line text-dark-500 text-sm"></i>
                    <input type="text" placeholder="Search..." class="bg-transparent text-dark-200 text-sm outline-none placeholder:text-dark-600 w-full">
                </div>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-xl text-dark-400 hover:text-neon-500 hover:bg-neon-500/5 transition-all">
                    <i class="ri-notification-3-line text-lg"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-neon-500" style="box-shadow: 0 0 6px oklch(0.66 0.17 195);"></span>
                </button>

                {{-- View site --}}
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-dark-300 glass neon-border hover:text-neon-400 transition-all">
                    <i class="ri-external-link-line"></i> View Site
                </a>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    /* Sidebar toggle (desktop collapse) */
    const sidebar    = document.getElementById('sidebar');
    const toggleBtn  = document.getElementById('sidebar-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    let collapsed = false;
    toggleBtn?.addEventListener('click', () => {
        collapsed = !collapsed;
        sidebar.classList.toggle('collapsed', collapsed);
        toggleIcon.className = collapsed ? 'ri-layout-right-line text-lg' : 'ri-layout-left-line text-lg';
    });

    /* Mobile sidebar */
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const overlay   = document.getElementById('sidebar-overlay');
    mobileBtn?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('show');
    });
    function closeMobileSidebar() {
        overlay.classList.remove('show');
    }
</script>

@stack('scripts')
</body>
</html>
