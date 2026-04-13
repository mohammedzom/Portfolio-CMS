@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your portfolio content')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    {{-- ── STATS GRID ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Total Projects', '6',   'ri-folder-4-line',     'neon',   '+2 this month', 'up'],
            ['Total Messages', '24',  'ri-mail-line',          'violet', '+8 new',        'up'],
            ['Profile Views',  '1.2K','ri-eye-line',           'blue',   '+18% this week','up'],
            ['Skills Listed',  '12',  'ri-bar-chart-2-line',  'green',  'All active',    'neutral'],
        ] as [$label, $value, $icon, $color, $sub, $trend])
        @php
        $colors = [
            'neon'   => ['from'=>'oklch(0.66 0.17 195)','to'=>'oklch(0.60 0.15 220)','glow'=>'oklch(0.66 0.17 195 / 0.25)','bg'=>'oklch(0.66 0.17 195 / 0.08)'],
            'violet' => ['from'=>'oklch(0.58 0.22 290)','to'=>'oklch(0.50 0.18 310)','glow'=>'oklch(0.58 0.22 290 / 0.25)','bg'=>'oklch(0.58 0.22 290 / 0.08)'],
            'blue'   => ['from'=>'oklch(0.58 0.18 250)','to'=>'oklch(0.52 0.16 270)','glow'=>'oklch(0.58 0.18 250 / 0.25)','bg'=>'oklch(0.58 0.18 250 / 0.08)'],
            'green'  => ['from'=>'oklch(0.62 0.18 160)','to'=>'oklch(0.56 0.15 180)','glow'=>'oklch(0.62 0.18 160 / 0.25)','bg'=>'oklch(0.62 0.18 160 / 0.08)'],
        ];
        $c = $colors[$color];
        @endphp
        <div class="stat-card glass rounded-2xl p-5 border border-dark-700 hover:border-neon-500/20 transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: {{ $c['bg'] }}; box-shadow: 0 0 16px {{ $c['glow'] }};">
                    <i class="{{ $icon }} text-xl" style="background: linear-gradient(135deg, {{ $c['from'] }}, {{ $c['to'] }}); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                @if($trend === 'up')
                <span class="text-xs font-medium px-2 py-1 rounded-full" style="background: oklch(0.62 0.18 160 / 0.1); color: oklch(0.72 0.18 160);">
                    <i class="ri-arrow-up-line"></i> Rising
                </span>
                @endif
            </div>
            <p class="font-display font-bold text-2xl text-dark-100">{{ $value }}</p>
            <p class="text-dark-400 text-sm mt-0.5">{{ $label }}</p>
            <p class="text-dark-600 text-xs mt-1">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── MAIN GRID ── --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Projects table --}}
        <div class="lg:col-span-2 glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-dark-700">
                <div>
                    <h2 class="font-display font-semibold text-dark-100">Recent Projects</h2>
                    <p class="text-dark-500 text-xs mt-0.5">Manage your portfolio projects</p>
                </div>
                <a href="{{ route('admin.projects') }}" class="inline-flex items-center gap-2 text-xs font-medium text-neon-500 hover:text-neon-400 transition-colors">
                    View all <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-dark-700">
                            <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-6 py-3">Project</th>
                            <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">Category</th>
                            <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Tech</th>
                            <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700">
                        @foreach([
                            ['Modern Website',      'Web',    'React + Tailwind',  'live'],
                            ['Landing Page',        'Web',    'Next.js',           'live'],
                            ['E-commerce Store',    'Web',    'Vue + Laravel',     'live'],
                            ['Mobile App UI',       'App',    'React Native',      'draft'],
                            ['Analytics Dashboard', 'UI/UX',  'Figma',             'live'],
                            ['Portfolio Design',    'Design', 'CSS + Figma',       'draft'],
                        ] as [$name,$cat,$tech,$status])
                        <tr class="hover:bg-dark-800/40 transition-colors group">
                            <td class="px-6 py-3.5">
                                <p class="text-dark-100 font-medium text-sm">{{ $name }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="text-xs px-2.5 py-1 rounded-full bg-neon-500/10 text-neon-400 font-medium">{{ $cat }}</span>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-dark-400 text-xs">{{ $tech }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($status === 'live')
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color: oklch(0.72 0.18 160);">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: oklch(0.72 0.18 160); box-shadow: 0 0 6px oklch(0.72 0.18 160);"></span> Live
                                </span>
                                @else
                                <span class="text-dark-500 text-xs flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-dark-500"></span> Draft
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="p-1.5 rounded-lg text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all">
                                        <i class="ri-pencil-line text-sm"></i>
                                    </button>
                                    <button class="p-1.5 rounded-lg text-dark-500 hover:text-red-400 hover:bg-red-500/5 transition-all">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">

            {{-- Quick actions --}}
            <div class="glass rounded-2xl border border-dark-700 p-5">
                <h2 class="font-display font-semibold text-dark-100 mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    @foreach([
                        ['ri-add-circle-line','Add New Project','admin.projects','neon'],
                        ['ri-message-3-line','View Messages','admin.messages','violet'],
                        ['ri-settings-3-line','Site Settings','admin.settings','blue'],
                        ['ri-external-link-line','Preview Portfolio','home','green'],
                    ] as [$icon,$label,$route,$color])
                    @php
                    $qColors = ['neon'=>'oklch(0.66 0.17 195)','violet'=>'oklch(0.58 0.22 290)','blue'=>'oklch(0.58 0.18 250)','green'=>'oklch(0.62 0.18 160)'];
                    @endphp
                    <a href="{{ route($route) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-dark-700/50 transition-all group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: {{ $qColors[$color] }}1a;">
                            <i class="{{ $icon }} text-sm" style="color: {{ $qColors[$color] }};"></i>
                        </div>
                        <span class="text-dark-300 text-sm font-medium group-hover:text-dark-100 transition-colors">{{ $label }}</span>
                        <i class="ri-arrow-right-s-line text-dark-600 ml-auto group-hover:text-dark-400 transition-colors"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Recent messages --}}
            <div class="glass rounded-2xl border border-dark-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display font-semibold text-dark-100">Recent Messages</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-neon-500/15 text-neon-400 font-semibold">3 new</span>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['John D.','Hey, I loved your portfolio! Are you available for a project?','2m ago',true],
                        ['Sarah M.','I need a website redesign. Can we talk?','1h ago',true],
                        ['Alex K.','Great work on the dashboard project!','3h ago',false],
                    ] as [$name,$msg,$time,$unread])
                    <div class="flex gap-3 items-start p-2.5 rounded-xl {{ $unread ? 'bg-neon-500/5' : '' }} hover:bg-dark-700/40 transition-colors cursor-pointer">
                        <div class="w-8 h-8 rounded-full gradient-neon flex items-center justify-center text-dark-950 text-xs font-bold shrink-0">
                            {{ substr($name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-dark-100 text-xs font-semibold">{{ $name }}</p>
                                @if($unread)<span class="w-1.5 h-1.5 rounded-full bg-neon-500 shrink-0"></span>@endif
                            </div>
                            <p class="text-dark-500 text-xs truncate mt-0.5">{{ $msg }}</p>
                        </div>
                        <span class="text-dark-600 text-xs shrink-0">{{ $time }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.messages') }}" class="block text-center text-xs text-neon-500 hover:text-neon-400 mt-4 transition-colors">
                    View all messages →
                </a>
            </div>
        </div>
    </div>

    {{-- ── SKILLS OVERVIEW ── --}}
    <div class="glass rounded-2xl border border-dark-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-display font-semibold text-dark-100">Skills Overview</h2>
                <p class="text-dark-500 text-xs mt-0.5">Your current skill levels</p>
            </div>
            <a href="{{ route('admin.skills') }}" class="inline-flex items-center gap-2 text-xs font-medium text-neon-500 hover:text-neon-400 transition-colors">
                Manage <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['HTML & CSS', 95, 'neon'],
                ['JavaScript', 90, 'neon'],
                ['React / Vue', 85, 'violet'],
                ['Tailwind CSS', 92, 'neon'],
                ['Node.js / PHP', 75, 'blue'],
                ['Figma / Design', 80, 'green'],
            ] as [$skill, $pct, $color])
            @php
            $sColors = ['neon'=>['oklch(0.66 0.17 195)','oklch(0.60 0.15 220)'],'violet'=>['oklch(0.58 0.22 290)','oklch(0.50 0.18 310)'],'blue'=>['oklch(0.58 0.18 250)','oklch(0.52 0.16 270)'],'green'=>['oklch(0.62 0.18 160)','oklch(0.56 0.15 180)']];
            [$from,$to] = $sColors[$color];
            @endphp
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-dark-200 font-medium">{{ $skill }}</span>
                    <span class="font-semibold" style="color: {{ $from }};">{{ $pct }}%</span>
                </div>
                <div class="h-2 rounded-full bg-dark-700 overflow-hidden">
                    <div class="h-full rounded-full" style="width: {{ $pct }}%; background: linear-gradient(90deg, {{ $from }}, {{ $to }}); box-shadow: 0 0 8px {{ $from }}66;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
