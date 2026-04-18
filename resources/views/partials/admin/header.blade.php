<header class="h-20 flex items-center justify-between px-6 border-b border-dark-800 bg-dark-900/50 backdrop-blur-xl shrink-0">
    <div class="flex items-center gap-4">
        {{-- Mobile hamburger --}}
        <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-xl text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all border border-dark-700">
            <i class="ri-menu-2-line text-xl"></i>
        </button>

        {{-- Page title --}}
        <div class="hidden sm:block">
            <h1 class="font-display font-bold text-dark-100 text-lg tracking-tight">@yield('page-title', 'Dashboard')</h1>
            <p class="text-dark-500 text-[10px] font-bold uppercase tracking-widest">@yield('page-subtitle', 'System Overview')</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Quick Search --}}
        <div class="hidden md:flex items-center gap-3 bg-dark-950/50 border border-dark-800 rounded-xl px-4 py-2 w-64 focus-within:border-neon-500/40 focus-within:ring-1 focus-within:ring-neon-500/20 transition-all group">
            <i class="ri-search-line text-dark-500 text-sm group-focus-within:text-neon-500 transition-colors"></i>
            <input type="text" placeholder="Quick search..." class="bg-transparent border-none p-0 text-dark-200 text-sm outline-none placeholder:text-dark-600 w-full focus:ring-0">
        </div>

        {{-- Notifications --}}
        <button class="relative p-2.5 rounded-xl text-dark-400 hover:text-neon-500 hover:bg-neon-500/5 border border-dark-800 transition-all group">
            <i class="ri-notification-3-line text-lg group-hover:scale-110 transition-transform"></i>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-neon-500 shadow-neon-sm animate-pulse"></span>
        </button>

        {{-- View site --}}
        <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-dark-300 bg-dark-800/50 border border-dark-700 hover:text-neon-400 hover:border-neon-500/40 hover:bg-neon-500/5 transition-all group">
            <i class="ri-external-link-line group-hover:scale-110 transition-transform"></i> 
            Live Site
        </a>
    </div>
</header>
