<header class="h-20 flex items-center justify-between px-6 lg:px-10 border-b border-dark-600/50 bg-dark-900/50 backdrop-blur-md shrink-0 z-20">
    <div class="flex items-center gap-4">
        {{-- Mobile hamburger --}}
        <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-xl text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all border border-dark-600/50">
            <i class="ri-menu-2-line text-xl"></i>
        </button>

        {{-- Page info --}}
        <div class="hidden sm:block">
            <div class="flex items-center gap-2 text-dark-500 text-[10px] font-black uppercase tracking-[0.2em]">
                <i class="ri-shield-user-line text-neon-500"></i>
                <span>Secure Admin Environment</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        {{-- View site --}}
        <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-dark-300 bg-dark-800 border border-dark-600/50 hover:text-neon-400 hover:border-neon-500/40 transition-all group">
            <i class="ri-external-link-line group-hover:scale-110 transition-transform"></i> 
            Live Site
        </a>

        <div class="h-8 w-px bg-dark-600/50 mx-2"></div>
        
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-black text-dark-100 leading-none">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[10px] font-bold text-neon-500 uppercase tracking-widest mt-1">Online</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-dark-800 border border-dark-600/50 flex items-center justify-center text-dark-400 shadow-sm">
                <i class="ri-user-3-line text-xl"></i>
            </div>
        </div>
    </div>
</header>
