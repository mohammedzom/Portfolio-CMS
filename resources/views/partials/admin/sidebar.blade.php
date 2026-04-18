@php
    use App\Models\Project;
    use App\Models\Message;
    use App\Models\Skill;
    use App\Models\Service;
    use App\Models\Experience;

    $projectsCount = Project::withoutTrashed()->count();
    $unreadMessagesCount = Message::withoutTrashed()->where('is_read', false)->count();
    $skillsCount = Skill::withoutTrashed()->count();
    $servicesCount = Service::withoutTrashed()->count();
    $experiencesCount = Experience::withoutTrashed()->count();

    $navItems = [
        ['route' => 'admin.dashboard', 'icon' => 'ri-dashboard-3-line', 'label' => 'Dashboard', 'badge' => null],
        ['route' => 'admin.projects.index', 'icon' => 'ri-folder-4-line', 'label' => 'Projects', 'badge' => $projectsCount],
        ['route' => 'admin.skills.index', 'icon' => 'ri-bar-chart-2-line', 'label' => 'Skills', 'badge' => $skillsCount],
        ['route' => 'admin.services.index', 'icon' => 'ri-service-line', 'label' => 'Services', 'badge' => $servicesCount],
        ['route' => 'admin.experience.index', 'icon' => 'ri-briefcase-line', 'label' => 'Experience', 'badge' => $experiencesCount],
    ];

    $bottomItems = [
        ['route' => 'admin.messages.index', 'icon' => 'ri-mail-line', 'label' => 'Messages', 'badge' => $unreadMessagesCount],
        ['route' => 'admin.settings.index', 'icon' => 'ri-settings-3-line', 'label' => 'Settings', 'badge' => null],
    ];
@endphp

<aside id="sidebar" class="relative z-30 flex flex-col w-64 shrink-0 border-r border-dark-600/50 bg-dark-900 transition-all duration-300 overflow-hidden lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0">
    {{-- Logo --}}
    <div class="flex items-center justify-between px-6 h-20 border-b border-dark-600/50 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-8 h-8 rounded-lg bg-neon-500 flex items-center justify-center shadow-[0_0_15px_oklch(0.66_0.17_195_/_0.3)] group-hover:scale-110 transition-transform">
                <i class="ri-code-s-slash-line text-dark-950 font-bold"></i>
            </div>
            <span class="font-display font-bold text-dark-100 text-lg tracking-tight">Patrick<span class="text-neon-500">.cms</span></span>
        </a>
        <button id="sidebar-close" class="lg:hidden p-2 text-dark-500 hover:text-neon-500">
            <i class="ri-close-line text-2xl"></i>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8">
        <div>
            <p class="text-dark-500 text-[10px] font-bold uppercase tracking-[0.2em] px-3 pb-4">Main Navigation</p>
            <div class="space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent transition-all duration-200 group {{ request()->routeIs($item['route'] . '*') ? 'bg-neon-500/10 border-neon-500/20 text-neon-400' : 'text-dark-400 hover:text-dark-100 hover:bg-dark-800' }}">
                        <i class="{{ $item['icon'] }} text-lg {{ request()->routeIs($item['route'] . '*') ? 'text-neon-400' : 'text-dark-500 group-hover:text-neon-400 transition-colors' }}"></i>
                        <span class="text-sm font-bold flex-1 tracking-tight">{{ $item['label'] }}</span>
                        @if ($item['badge'] > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-lg {{ request()->routeIs($item['route'] . '*') ? 'bg-neon-500 text-dark-950' : 'bg-dark-800 text-dark-500' }} font-black transition-all">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-dark-500 text-[10px] font-bold uppercase tracking-[0.2em] px-3 pb-4">System</p>
            <div class="space-y-1">
                @foreach ($bottomItems as $item)
                    <a href="{{ route($item['route']) }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent transition-all duration-200 group {{ request()->routeIs($item['route'] . '*') ? 'bg-neon-500/10 border-neon-500/20 text-neon-400' : 'text-dark-400 hover:text-dark-100 hover:bg-dark-800' }}">
                        <i class="{{ $item['icon'] }} text-lg {{ request()->routeIs($item['route'] . '*') ? 'text-neon-400' : 'text-dark-500 group-hover:text-neon-400 transition-colors' }}"></i>
                        <span class="text-sm font-bold flex-1 tracking-tight">{{ $item['label'] }}</span>
                        @if ($item['badge'] > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-lg {{ request()->routeIs($item['route'] . '*') ? 'bg-neon-500 text-dark-950' : 'bg-dark-800 text-dark-500' }} font-black transition-all">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- User profile --}}
    <div class="px-4 pb-6 pt-4 border-t border-dark-600/50">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-dark-950/30 border border-dark-600/30 mb-3">
            <div class="w-10 h-10 rounded-xl bg-neon-500 flex items-center justify-center text-dark-950 font-black text-sm shrink-0 shadow-[0_0_15px_oklch(0.66_0.17_195_/_0.3)]">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-dark-100 text-xs font-bold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-dark-500 text-[10px] font-bold uppercase tracking-widest truncate">System Admin</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 transition-all duration-300 text-xs font-black uppercase tracking-widest group">
                <i class="ri-logout-box-r-line group-hover:scale-110 transition-transform"></i>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-dark-950/80 backdrop-blur-sm z-20 lg:hidden hidden"></div>

@push('scripts')
<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');

    mobileMenuBtn?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    });

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    };

    sidebarClose?.addEventListener('click', closeSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);
</script>
@endpush
