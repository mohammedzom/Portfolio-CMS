@extends('layouts.app')

@section('title', ($settings->first_name ?? 'Patrick') . ' ' . ($settings->last_name ?? 'CMS') . ' — ' . ($settings->tagline ?? 'Full-Stack Engineer'))

@section('content')
    {{-- HERO SECTION --}}
    <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        {{-- Ambient Blobs --}}
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-neon-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="container-custom relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-neon-500/10 border border-neon-500/20 text-neon-400 text-xs font-bold uppercase tracking-widest">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-neon-500"></span>
                        </span>
                        {{ $settings->available_for_freelance ? 'Available for new projects' : 'Currently Booked' }}
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-dark-400 text-lg font-medium tracking-wide">Hello, I'm</h2>
                        <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-none">
                            <span class="gradient-text-white block">{{ $settings->first_name ?? 'Patrick' }}</span>
                            <span class="gradient-text block">{{ $settings->last_name ?? 'Zomlot' }}</span>
                        </h1>
                        <p class="text-xl md:text-2xl text-dark-300 font-medium max-w-xl">
                            {{ $settings->tagline ?? 'Building the future of the web, one pixel at a time.' }}
                        </p>
                    </div>

                    <p class="text-dark-400 text-lg leading-relaxed max-w-lg">
                        {{ $settings->bio ?? 'Passionate full-stack developer focused on creating clean, performant, and user-centric digital experiences.' }}
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <x-button variant="neon" size="lg" href="#projects">
                            View My Work <i class="ri-arrow-right-line"></i>
                        </x-button>
                        <x-button variant="secondary" size="lg" href="#contact">
                            Get In Touch
                        </x-button>
                    </div>
                </div>

                <div class="relative hidden lg:block animate-fade-in" style="animation-delay: 0.2s">
                    <div class="relative w-full aspect-square max-w-md mx-auto">
                        {{-- Decorative Rings --}}
                        <div class="absolute inset-0 border-2 border-dark-800 rounded-3xl rotate-6 scale-105"></div>
                        <div class="absolute inset-0 border-2 border-neon-500/20 rounded-3xl -rotate-3 scale-105"></div>
                        
                        <div class="relative h-full w-full rounded-3xl overflow-hidden border border-dark-700 bg-dark-900 group">
                            <img src="{{ $settings->avatar ? Storage::url($settings->avatar) : asset('assets/img/perfil.png') }}" 
                                 alt="{{ $settings->first_name ?? 'Profile' }}" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 via-transparent to-transparent"></div>
                        </div>

                        {{-- Floating Stats --}}
                        <div class="absolute -bottom-6 -left-6">
                            <x-card padding="p-4" class="shadow-neon-sm">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl font-black text-neon-400">{{ $settings->years_experience ?? '5' }}+</div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-dark-500 leading-tight">Years<br>Experience</div>
                                </div>
                            </x-card>
                        </div>
                        <div class="absolute -top-6 -right-6">
                            <x-card padding="p-4" class="shadow-neon-sm">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl font-black text-neon-400">{{ $settings->projects_count ?? '20' }}+</div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-dark-500 leading-tight">Projects<br>Completed</div>
                                </div>
                            </x-card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT SECTION --}}
    <section id="about" class="py-24 relative overflow-hidden">
        <div class="container-custom relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 space-y-8">
                    <div class="space-y-4">
                        <span class="text-neon-500 font-bold uppercase tracking-[0.3em] text-xs">About Me</span>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Professional Journey</h2>
                    </div>
                    
                    <div class="prose prose-invert max-w-none text-dark-400 leading-relaxed space-y-4">
                        {!! nl2br(e($settings->about_me ?? 'With years of experience in full-stack development, I specialize in building robust applications using modern technologies.')) !!}
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-dark-500">Location</p>
                            <p class="text-dark-200 font-medium">{{ $settings->location ?? 'Global / Remote' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-dark-500">Languages</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($languages ?? [] as $lang)
                                    <span class="text-dark-200 font-medium">{{ $lang['name'] ?? '' }} ({{ $lang['level'] ?? '' }})</span>
                                @empty
                                    <span class="text-dark-400">English, Arabic</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <x-button variant="primary" size="lg" href="{{ $settings->cv_file ? Storage::url($settings->cv_file) : '#' }}" target="_blank">
                            <i class="ri-download-line"></i> Download Resume
                        </x-button>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-8">
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-dark-200 flex items-center gap-3">
                            <span class="w-8 h-px bg-neon-500"></span> Experience
                        </h3>
                        <div class="space-y-8 relative before:absolute before:inset-y-0 before:left-[11px] before:w-px before:bg-dark-800">
                            @forelse($experiences ?? [] as $exp)
                                <div class="relative pl-10">
                                    <div class="absolute left-0 top-1.5 w-6 h-6 rounded-full bg-dark-900 border-2 border-neon-500 flex items-center justify-center shadow-neon-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-neon-500"></div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <h4 class="text-lg font-bold text-dark-100">{{ $exp->job_title }}</h4>
                                            <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded bg-dark-800 text-dark-400 border border-dark-700">
                                                {{ $exp->start_date }} — {{ $exp->is_current ? 'Present' : $exp->end_date }}
                                            </span>
                                        </div>
                                        <p class="text-neon-500 text-sm font-medium">{{ $exp->company }}</p>
                                        <p class="text-dark-400 text-sm leading-relaxed mt-2">{{ $exp->description }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-dark-500 italic pl-10">No experience records found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SERVICES SECTION --}}
    <section id="services" class="py-24 bg-dark-900/30">
        <div class="container-custom">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-neon-500 font-bold uppercase tracking-[0.3em] text-xs">Expertise</span>
                <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Specialized Services</h2>
                <p class="text-dark-400">Transforming complex requirements into elegant digital solutions through a structured and innovative approach.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services ?? [] as $service)
                    <x-card hover="true" class="group">
                        <div class="space-y-6">
                            <div class="w-14 h-14 rounded-2xl bg-dark-800 border border-dark-700 flex items-center justify-center text-neon-400 text-3xl group-hover:bg-neon-500 group-hover:text-dark-950 group-hover:scale-110 transition-all duration-500">
                                <i class="{{ $service->icon ?? 'ri-stack-line' }}"></i>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-xl font-bold text-dark-100 group-hover:text-neon-400 transition-colors">{{ $service->title }}</h3>
                                <p class="text-dark-400 text-sm leading-relaxed">{{ $service->description }}</p>
                            </div>
                            @if($service->tags)
                                <div class="flex flex-wrap gap-2 pt-2">
                                    @foreach($service->tags as $tag)
                                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg bg-dark-950/50 text-dark-500 border border-dark-800">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full text-center py-12 text-dark-500 italic">No services listed yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- SKILLS SECTION --}}
    <section id="skills" class="py-24">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <span class="text-neon-500 font-bold uppercase tracking-[0.3em] text-xs">Capabilities</span>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Technical Arsenal</h2>
                        <p class="text-dark-400 max-w-md">I continuously evolve my skill set to stay at the forefront of modern web development and software engineering.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach($skills->groupBy('type') as $type => $group)
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-dark-500 border-b border-dark-800 pb-2">{{ $type ?: 'Other' }}</h4>
                                <div class="space-y-3">
                                    @foreach($group as $skill)
                                        <div class="flex items-center gap-3 group">
                                            <div class="w-8 h-8 rounded-lg bg-dark-900 border border-dark-800 flex items-center justify-center text-dark-400 group-hover:border-neon-500/50 group-hover:text-neon-400 transition-all">
                                                <i class="{{ $skill->icon ?? 'ri-terminal-box-line' }}"></i>
                                            </div>
                                            <span class="text-sm font-medium text-dark-300 group-hover:text-dark-100 transition-colors">{{ $skill->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    @foreach($skills->where('proficiency', '>', 80)->take(4) as $topSkill)
                        <x-card class="relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <i class="{{ $topSkill->icon }} text-6xl"></i>
                            </div>
                            <div class="space-y-4 relative z-10">
                                <div class="text-4xl font-black text-neon-400">{{ $topSkill->proficiency }}%</div>
                                <div class="space-y-1">
                                    <h4 class="text-lg font-bold text-dark-100">{{ $topSkill->name }}</h4>
                                    <div class="h-1 w-full bg-dark-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-neon-500 shadow-neon-sm transition-all duration-1000" style="width: {{ $topSkill->proficiency }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- PROJECTS SECTION --}}
    <section id="projects" class="py-24 bg-dark-900/30">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
                <div class="space-y-4">
                    <span class="text-neon-500 font-bold uppercase tracking-[0.3em] text-xs">Portfolio</span>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Featured Projects</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest bg-neon-500 text-dark-950 shadow-neon-sm transition-all">All</button>
                    @foreach($projects->pluck('category')->unique() as $cat)
                        <button class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest bg-dark-800 text-dark-400 border border-dark-700 hover:border-neon-500/50 hover:text-neon-400 transition-all">{{ $cat }}</button>
                    @endforeach
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    <article class="group relative bg-dark-900 rounded-3xl overflow-hidden border border-dark-800 hover:border-neon-500/30 transition-all duration-500 shadow-xl">
                        <div class="aspect-video overflow-hidden relative">
                            <img src="{{ !empty($project->images) ? Storage::url($project->images[0]) : 'https://placehold.co/600x400/18181b/a1a1aa?text=Project+Image' }}" 
                                 alt="{{ $project->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 grayscale group-hover:grayscale-0">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950/90 via-dark-950/20 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                            
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-lg bg-dark-950/80 backdrop-blur-md border border-dark-700 text-[10px] font-black uppercase tracking-widest text-neon-400">
                                    {{ $project->category }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-black text-dark-100 group-hover:text-neon-400 transition-colors">{{ $project->title }}</h3>
                                <p class="text-dark-400 text-sm line-clamp-2 leading-relaxed">{{ $project->description }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @foreach($project->tech_stack as $tech)
                                    <span class="text-[10px] font-bold text-dark-500 px-2 py-1 rounded bg-dark-950 border border-dark-800">{{ $tech }}</span>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t border-dark-800">
                                @if($project->live_url)
                                    <a href="{{ $project->live_url }}" target="_blank" class="text-neon-400 hover:text-neon-300 transition-colors flex items-center gap-2 text-sm font-bold">
                                        <i class="ri-external-link-line"></i> Live Demo
                                    </a>
                                @endif
                                @if($project->repo_url)
                                    <a href="{{ $project->repo_url }}" target="_blank" class="text-dark-400 hover:text-dark-100 transition-colors flex items-center gap-2 text-sm font-bold">
                                        <i class="ri-github-line"></i> Repository
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-24">
                        <div class="text-dark-700 text-6xl mb-4"><i class="ri-inbox-line"></i></div>
                        <p class="text-dark-500 italic">Portfolio is currently being updated. Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- CONTACT SECTION --}}
    <section id="contact" class="py-24 relative overflow-hidden">
        <div class="container-custom relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="grid lg:grid-cols-5 gap-12 items-start">
                    <div class="lg:col-span-2 space-y-12">
                        <div class="space-y-4">
                            <span class="text-neon-500 font-bold uppercase tracking-[0.3em] text-xs">Get In Touch</span>
                            <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Let's Create Something Great</h2>
                            <p class="text-dark-400 leading-relaxed">Have a bold idea or a challenging project? I'm always open to discussing new opportunities and creative collaborations.</p>
                        </div>

                        <div class="space-y-6">
                            <a href="mailto:{{ $settings->email ?? 'hello@example.com' }}" class="flex items-center gap-4 group p-4 rounded-2xl bg-dark-900/50 border border-dark-800 hover:border-neon-500/30 transition-all">
                                <div class="w-12 h-12 rounded-xl bg-dark-800 flex items-center justify-center text-neon-400 text-xl group-hover:bg-neon-500 group-hover:text-dark-950 transition-all">
                                    <i class="ri-mail-send-line"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-dark-500">Email Me</p>
                                    <p class="text-dark-200 font-medium">{{ $settings->email ?? 'hello@example.com' }}</p>
                                </div>
                            </a>

                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-dark-900/50 border border-dark-800">
                                <div class="w-12 h-12 rounded-xl bg-dark-800 flex items-center justify-center text-neon-400 text-xl">
                                    <i class="ri-map-pin-2-line"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-dark-500">Location</p>
                                    <p class="text-dark-200 font-medium">{{ $settings->location ?? 'Remote / Global' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-dark-500">Social Connections</p>
                            <div class="flex gap-3">
                                @foreach($social_links ?? [] as $link)
                                    <a href="{{ $link['url'] }}" target="_blank" class="w-12 h-12 rounded-xl bg-dark-900 border border-dark-800 flex items-center justify-center text-dark-400 hover:text-neon-500 hover:border-neon-500/40 hover:scale-110 transition-all duration-300">
                                        <i class="ri-{{ strtolower($link['name'] ?? 'link') }}-line text-xl"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <x-card class="shadow-2xl">
                            <form action="{{ route('home') }}" method="POST" class="space-y-6">
                                @csrf
                                <div class="grid md:grid-cols-2 gap-6">
                                    <x-input label="Your Name" name="name" placeholder="John Doe" required="true" />
                                    <x-input label="Email Address" name="email" type="email" placeholder="john@example.com" required="true" />
                                </div>
                                <x-input label="Subject" name="subject" placeholder="Project Inquiry" required="true" />
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Message <span class="text-neon-500">*</span></label>
                                    <textarea name="message" rows="6" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none" placeholder="Tell me about your project..."></textarea>
                                </div>
                                <x-button variant="neon" size="lg" type="submit" class="w-full">
                                    Send Message <i class="ri-send-plane-fill"></i>
                                </x-button>
                            </form>
                        </x-card>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
