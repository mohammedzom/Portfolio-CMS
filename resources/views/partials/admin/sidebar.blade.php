<div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-lg">
            <i class="ri-dashboard-3-line font-bold"></i>
        </div>
        <span class="text-xl font-black tracking-tight text-slate-900">Admin<span class="text-indigo-600">Panel</span></span>
    </div>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    @php
                        \$navItems = [
                            ['route' => 'admin.dashboard', 'icon' => 'ri-home-4-line', 'label' => 'Dashboard'],
                            ['route' => 'admin.projects.index', 'icon' => 'ri-folder-open-line', 'label' => 'Projects'],
                            ['route' => 'admin.skills.index', 'icon' => 'ri-medal-line', 'label' => 'Skills'],
                            ['route' => 'admin.services.index', 'icon' => 'ri-service-line', 'label' => 'Services'],
                            ['route' => 'admin.experience.index', 'icon' => 'ri-briefcase-line', 'label' => 'Experience'],
                            ['route' => 'admin.messages.index', 'icon' => 'ri-mail-line', 'label' => 'Messages'],
                            ['route' => 'admin.settings.index', 'icon' => 'ri-settings-3-line', 'label' => 'Settings'],
                        ];
                    @endphp

                    @foreach(\$navItems as \$item)
                        <li>
                            <a href="{{ route(\$item['route']) }}" class="{{ request()->routeIs(\$item['route'].'*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:text-indigo-600 hover:bg-slate-50' }} group flex gap-x-3 rounded-xl p-3 text-sm font-semibold leading-6 transition-all duration-200">
                                <i class="{{ \$item['icon'] }} text-lg {{ request()->routeIs(\$item['route'].'*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                                {{ \$item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="mt-auto">
                <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Quick Stats</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-2xl font-black text-white">{{ \App\Models\Project::count() }}</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase">Projects</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-indigo-400">{{ \App\Models\Message::where('is_read', false)->count() }}</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase">New Msgs</p>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</div>
