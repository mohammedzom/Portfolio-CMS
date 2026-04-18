@extends('layouts.app')

@section('title', ($settings->first_name ?? 'Patrick') . ' ' . ($settings->last_name ?? 'CMS') . ' — ' . ($settings->tagline ?? 'Full-Stack Engineer'))

@section('content')
    {{-- HERO SECTION --}}
    <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        {{-- Ambient Blobs --}}
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-neon-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-neon-500/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="container-custom relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-neon-500/10 border border-neon-500/20 text-neon-400 text-[10px] font-black uppercase tracking-widest">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-neon-500"></span>
                        </span>
                        {{ $settings->available_for_freelance ? 'Available for new projects' : 'Currently Booked' }}
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-dark-400 text-lg font-bold tracking-widest uppercase text-xs">Hello, I'm</h2>
                        <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-none">
                            <span class="gradient-text-white block">{{ $settings->first_name ?? 'Patrick' }}</span>
                            <span class="gradient-text block">{{ $settings->last_name ?? 'Zomlot' }}</span>
                        </h1>
                        <p class="text-xl md:text-2xl text-dark-300 font-bold max-w-xl">
                            {{ $settings->tagline ?? 'Building the future of the web, one pixel at a time.' }}
                        </p>
                    </div>

                    <p class="text-dark-400 text-lg leading-relaxed max-w-lg font-medium">
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
                        
                        <div class="relative h-full w-full rounded-3xl overflow-hidden border border-dark-600 bg-dark-900 group shadow-2xl">
                            <img src="{{ $settings->avatar ? Storage::url($settings->avatar) : asset('assets/img/perfil.png') }}" 
                                 alt="{{ $settings->first_name ?? 'Profile' }}" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 via-transparent to-transparent"></div>
                        </div>

                        {{-- Floating Stats --}}
                        <div class="absolute -bottom-6 -left-6">
                            <x-card padding="p-4" class="shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.2)]">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl font-black text-neon-500">{{ $settings->years_experience ?? '5' }}+</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-dark-500 leading-tight">Years<br>Experience</div>
                                </div>
                            </x-card>
                        </div>
                        <div class="absolute -top-6 -right-6">
                            <x-card padding="p-4" class="shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.2)]">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl font-black text-neon-500">{{ $settings->projects_count ?? '20' }}+</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-dark-500 leading-tight">Projects<br>Completed</div>
                                </div>
                            </x-card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT SECTION --}}
    <section id="about" class="py-24 relative overflow-hidden bg-dark-900/50">
        <div class="container-custom relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 space-y-8">
                    <div class="space-y-4">
                        <span class="text-neon-500 font-black uppercase tracking-[0.3em] text-[10px]">About Me</span>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Professional Journey</h2>
                    </div>
                    
                    <div class="prose prose-invert max-w-none text-dark-400 leading-relaxed space-y-4 font-medium">
                        {!! nl2br(e($settings->about_me ?? 'With years of experience in full-stack development, I specialize in building robust applications using modern technologies.')) !!}
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Location</p>
                            <p class="text-dark-200 font-bold">{{ $settings->location ?? 'Global / Remote' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Languages</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($languages ?? [] as $lang)
                                    <span class="text-dark-200 font-bold">{{ $lang['name'] ?? '' }} ({{ $lang['level'] ?? '' }})</span>
                                @empty
                                    <span class="text-dark-400 font-bold">English, Arabic</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <x-button variant="neon" size="lg" href="{{ $settings->cv_file ? Storage::url($settings->cv_file) : '#' }}" target="_blank">
                            <i class="ri-download-line"></i> Download Resume
                        </x-button>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-8">
                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-dark-100 flex items-center gap-3 uppercase tracking-widest text-sm">
                            <span class="w-8 h-px bg-neon-500"></span> Experience
                        </h3>
                        <div class="space-y-8 relative before:absolute before:inset-y-0 before:left-[11px] before:w-px before:bg-dark-800">
                            @forelse($experiences ?? [] as $exp)
                                <div class="relative pl-10 group">
                                    <div class="absolute left-0 top-1.5 w-6 h-6 rounded-full bg-dark-950 border-2 border-neon-500 flex items-center justify-center shadow-[0_0_10px_oklch(0.66_0.17_195_/_0.3)] group-hover:scale-110 transition-transform">
                                        <div class="w-1.5 h-1.5 rounded-full bg-neon-500"></div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <h4 class="text-lg font-black text-dark-100">{{ $exp->job_title }}</h4>
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-dark-800 text-dark-400 border border-dark-600/50">
                                                {{ $exp->start_date }} — {{ $exp->is_current ? 'Present' : $exp->end_date }}
                                            </span>
                                        </div>
                                        <p class="text-neon-500 text-xs font-black uppercase tracking-widest">{{ $exp->company }}</p>
                                        <p class="text-dark-400 text-sm leading-relaxed mt-2 font-medium">{{ $exp->description }}</p>
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
    <section id="services" class="py-24">
        <div class="container-custom">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-neon-500 font-black uppercase tracking-[0.3em] text-[10px]">Expertise</span>
                <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Specialized Services</h2>
                <p class="text-dark-400 font-medium">Transforming complex requirements into elegant digital solutions through a structured and innovative approach.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services ?? [] as $service)
                    <x-card hover="true" class="group">
                        <div class="space-y-6">
                            <div class="w-14 h-14 rounded-2xl bg-dark-900 border border-dark-600/50 flex items-center justify-center text-neon-500 text-3xl group-hover:bg-neon-500 group-hover:text-dark-950 group-hover:scale-110 transition-all duration-500 shadow-sm">
                                <i class="{{ $service->icon ?? 'ri-stack-line' }}"></i>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-xl font-black text-dark-100 group-hover:text-neon-500 transition-colors tracking-tight">{{ $service->title }}</h3>
                                <p class="text-dark-400 text-sm leading-relaxed font-medium">{{ $service->description }}</p>
                            </div>
                            @if($service->tags)
                                <div class="flex flex-wrap gap-2 pt-2">
                                    @foreach(is_array($service->tags) ? $service->tags : explode(',', $service->tags) as $tag)
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg bg-dark-950 text-dark-500 border border-dark-600/50">{{ trim($tag) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-card>
                @empty
                    <p class="text-center col-span-full text-dark-500">No services found.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- SKILLS SECTION --}}
    <section id="skills" class="py-24 bg-dark-900/50">
        <div class="container-custom">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-neon-500 font-black uppercase tracking-[0.3em] text-[10px]">Proficiency</span>
                <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Technical Stack</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($skills ?? [] as $skill)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-1">
                            <div class="flex items-center gap-3">
                                <i class="{{ $skill->icon ?? 'ri-code-line' }} text-neon-500 text-xl"></i>
                                <span class="text-sm font-black text-dark-100 uppercase tracking-widest">{{ $skill->name }}</span>
                            </div>
                            <span class="text-xs font-black text-neon-500">{{ $skill->level }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-dark-800 rounded-full overflow-hidden border border-dark-600/30">
                            <div class="h-full gradient-neon rounded-full shadow-[0_0_10px_oklch(0.66_0.17_195_/_0.5)] transition-all duration-1000" style="width: {{ $skill->level }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center col-span-full text-dark-500">No skills found.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- PROJECTS SECTION --}}
    <section id="projects" class="py-24">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="space-y-4">
                    <span class="text-neon-500 font-black uppercase tracking-[0.3em] text-[10px]">Portfolio</span>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tight text-dark-100">Featured Projects</h2>
                </div>
                <x-button variant="secondary" size="md">View All Work</x-button>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects ?? [] as $project)
                    <div class="group relative bg-dark-900 border border-dark-600/50 rounded-3xl overflow-hidden hover:border-neon-500/30 transition-all duration-500 shadow-xl">
                        <div class="aspect-video overflow-hidden relative">
                            @if(!empty($project->images))
                                <img src="{{ Storage::url($project->images[0]) }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 grayscale group-hover:grayscale-0">
                            @endif
                            <div class="absolute inset-0 bg-dark-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                @if($project->live_url)
                                    <a href="{{ $project->live_url }}" target="_blank" class="w-12 h-12 rounded-xl bg-neon-500 text-dark-950 flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                                        <i class="ri-external-link-line text-xl font-bold"></i>
                                    </a>
                                @endif
                                @if($project->repo_url)
                                    <a href="{{ $project->repo_url }}" target="_blank" class="w-12 h-12 rounded-xl bg-dark-950 text-neon-500 border border-neon-500/50 flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                                        <i class="ri-github-line text-xl font-bold"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="p-8 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase tracking-widest text-neon-500 bg-neon-500/10 px-2 py-1 rounded-lg border border-neon-500/20">{{ $project->category }}</span>
                            </div>
                            <h3 class="text-xl font-black text-dark-100 tracking-tight">{{ $project->title }}</h3>
                            <p class="text-sm text-dark-400 line-clamp-2 leading-relaxed font-medium">{{ $project->description }}</p>
                            
                            <div class="flex flex-wrap gap-2 pt-2">
                                @foreach(is_array($project->tech_stack) ? $project->tech_stack : explode(',', $project->tech_stack) as $tech)
                                    <span class="text-[9px] font-black text-dark-500 bg-dark-800 px-2 py-1 rounded border border-dark-600/50 uppercase tracking-widest">{{ trim($tech) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center col-span-full text-dark-500">No projects found.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- CONTACT SECTION --}}
    <section id="contact" class="py-24 bg-dark-900/50">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto bg-dark-900 border border-dark-600 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="grid lg:grid-cols-2">
                    <div class="p-10 lg:p-16 space-y-12 bg-neon-500">
                        <div class="space-y-4">
                            <h2 class="text-4xl font-black text-dark-950 tracking-tighter uppercase leading-none">Let's <br>Connect.</h2>
                            <p class="text-dark-900/70 font-bold leading-relaxed">Have a project in mind? Let's discuss how we can build something amazing together.</p>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-dark-950 flex items-center justify-center text-neon-500 text-xl shadow-lg">
                                    <i class="ri-mail-send-line"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-900/50">Email Address</p>
                                    <p class="text-dark-950 font-black">{{ $settings->email ?? 'contact@example.com' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            @foreach($social_links ?? [] as $social)
                                <a href="{{ $social->url }}" target="_blank" class="w-10 h-10 rounded-xl bg-dark-950 flex items-center justify-center text-neon-500 hover:scale-110 transition-transform shadow-lg">
                                    <i class="{{ $social->icon }} text-lg"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-10 lg:p-16">
                        <form action="{{ route('messages.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <x-input label="Full Name" name="name" placeholder="John Doe" required="true" />
                            <x-input label="Email Address" name="email" type="email" placeholder="john@example.com" required="true" />
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-dark-400 uppercase tracking-widest px-1">Your Message</label>
                                <textarea name="message" rows="4" required class="w-full bg-dark-950 border-dark-600 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none font-medium" placeholder="How can I help you?"></textarea>
                            </div>
                            <x-button variant="neon" size="lg" type="submit" class="w-full">Send Message</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
