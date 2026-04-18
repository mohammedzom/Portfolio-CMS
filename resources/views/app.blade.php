<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $settings->first_name }} {{ $settings->last_name }} — {{ $settings->bio }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    {{-- Remixicons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Vite (Tailwind) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ $settings->first_name }} {{ $settings->last_name }} — {{ $settings->bio }}</title>

    <style>
        /* Mesh gradient blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: oklch(0.66 0.17 195 / 0.12);
            top: -150px;
            left: -100px;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: oklch(0.60 0.15 220 / 0.10);
            top: 200px;
            right: -80px;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: oklch(0.66 0.17 195 / 0.08);
            bottom: 0;
            left: 30%;
        }

        /* Typing cursor */
        .cursor::after {
            content: '|';
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        /* Skill bar */
        .skill-bar-fill {
            height: 4px;
            border-radius: 99px;
            background: linear-gradient(90deg, oklch(0.66 0.17 195), oklch(0.60 0.15 220));
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 8px oklch(0.66 0.17 195 / 0.5);
        }

        /* Nav active */
        .nav-link.active {
            color: oklch(0.66 0.17 195);
        }

        .nav-link.active::after {
            width: 100%;
        }

        .nav-link::after {
            content: '';
            display: block;
            height: 2px;
            background: oklch(0.66 0.17 195);
            width: 0;
            transition: width 0.3s ease;
            border-radius: 99px;
            box-shadow: 0 0 6px oklch(0.66 0.17 195 / 0.6);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Project card overlay */
        .project-card:hover .project-overlay {
            opacity: 1;
        }

        .project-card:hover .project-img {
            transform: scale(1.05);
        }

        .project-overlay {
            opacity: 0;
            transition: opacity 0.4s ease;
            background: linear-gradient(to top, oklch(0.10 0.01 255 / 0.95) 0%, oklch(0.10 0.01 255 / 0.5) 60%, transparent 100%);
        }

        .project-img {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Section reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Mobile menu */
        #mobile-menu {
            display: none;
        }

        #mobile-menu.open {
            display: flex;
        }

        /* Glowing button */
        .btn-neon {
            position: relative;
            overflow: hidden;
        }

        .btn-neon::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, oklch(0.66 0.17 195), oklch(0.60 0.15 220));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .btn-neon:hover::before {
            opacity: 1;
        }

        .btn-neon>* {
            position: relative;
            z-index: 1;
        }

        /* Timeline dot */
        .timeline-dot::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 6px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: oklch(0.66 0.17 195);
            box-shadow: 0 0 12px oklch(0.66 0.17 195 / 0.6);
        }
    </style>
</head>

<body class="antialiased selection:bg-neon-500/20 selection:text-neon-300">

    {{-- ===================== NAVBAR ===================== --}}
    <header id="navbar" class="fixed top-0 inset-x-0 z-50 transition-all duration-500">
        <div class="container-custom">
            <nav class="flex items-center justify-between h-16 md:h-20">
                {{-- Logo --}}
                <a href="#home" class="font-display font-bold text-xl text-dark-100 tracking-tight group">
                    {{ $settings->url_prefix }}<span
                        class="text-neon-500 group-hover:text-neon-400 transition-colors">.{{ $settings->url_suffix }}</span>
                </a>

                {{-- Desktop Links --}}
                <ul class="hidden md:flex items-center gap-8">
                    @foreach ([['#home', 'Home'], ['#about', 'About'], ['#skills', 'Skills'], ['#services', 'Services'], ['#projects', 'Projects'], ['#contact', 'Contact']] as [$href, $label])
                        <li>
                            <a href="{{ $href }}"
                                class="nav-link text-sm font-medium text-dark-300 hover:text-dark-100 transition-colors">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>

                {{-- CTA + Hamburger --}}
                <div class="flex items-center gap-4">
                    <a href="#contact"
                        class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold text-dark-950 gradient-neon neon-glow hover:scale-105 transition-transform duration-300">
                        Hire Me <i class="ri-arrow-right-up-line text-base"></i>
                    </a>
                    <button id="hamburger" class="md:hidden text-dark-300 hover:text-neon-500 transition-colors p-1">
                        <i id="hamburger-icon" class="ri-menu-3-line text-2xl"></i>
                    </button>
                </div>
            </nav>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="flex-col gap-1 pb-4 md:hidden">
                @foreach ([['#home', 'Home'], ['#about', 'About'], ['#skills', 'Skills'], ['#services', 'Services'], ['#projects', 'Projects'], ['#contact', 'Contact']] as [$href, $label])
                    <a href="{{ $href }}"
                        class="mobile-nav-link block px-4 py-3 rounded-xl text-dark-300 hover:text-neon-500 hover:bg-neon-500/5 font-medium transition-all text-sm">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </header>

    {{-- ===================== HERO ===================== --}}
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden">
        {{-- Blobs --}}
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        {{-- Grid texture --}}
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: linear-gradient(oklch(0.80 0.004 255) 1px, transparent 1px), linear-gradient(90deg, oklch(0.80 0.004 255) 1px, transparent 1px); background-size: 60px 60px;">
        </div>

        <div class="container-custom relative z-10 pt-24 pb-16">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left --}}
                <div class="space-y-8 animate-[fade-up_0.8s_ease_both]">
                    {{-- Badge --}}
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass neon-border text-neon-400 text-sm font-medium">
                        <span class="w-2 h-2 rounded-full bg-neon-500 animate-pulse"></span>
                        @if ($settings->available_for_freelance)
                            Available for freelance work
                        @else
                            Not available for freelance work
                        @endif
                    </div>

                    {{-- Headline --}}
                    <div class="space-y-3">
                        <p class="text-dark-300 font-medium tracking-widest uppercase text-sm">Hello, I'm</p>
                        <h1 class="font-display font-bold leading-none text-5xl sm:text-6xl lg:text-7xl">
                            <span class="gradient-text-white">{{ $settings->first_name }}</span><br>
                            <span class="gradient-text">{{ $settings->last_name }}</span>
                        </h1>
                        <p class="text-dark-300 text-xl font-medium">
                            {{ $settings->tagline }}
                        </p>
                    </div>

                    {{-- Description --}}
                    <p class="text-dark-400 text-base leading-relaxed max-w-md">
                        {{ $settings->bio }}
                    </p>

                    {{-- CTAs --}}
                    <div class="flex flex-wrap gap-4">
                        <a href="#projects"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-dark-950 gradient-neon neon-glow hover:scale-105 transition-transform duration-300">
                            View My Work <i class="ri-arrow-right-line"></i>
                        </a>
                        <a href="#contact"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-dark-100 glass neon-border hover:border-neon-500/50 hover:text-neon-400 transition-all duration-300">
                            Get In Touch <i class="ri-send-plane-line"></i>
                        </a>
                    </div>

                    {{-- Social --}}
                    <div class="flex items-center gap-4 pt-2">
                        @forelse ($social_links as $link)
                            <a href="{{ $link['url'] }}" target="_blank"
                                class="w-10 h-10 rounded-xl glass neon-border flex items-center justify-center text-dark-400 hover:text-neon-500 hover:border-neon-500/40 hover:scale-110 transition-all duration-300">
                                {{-- Todo: Add social icons --}}
                                <i class="{{ $link['icon'] }} text-xl"></i>

                                {{-- <img src="{{ $link['icon'] }}" alt="{{ $link['url'] }}" class="w-6 h-6"> --}}
                            </a>
                        @empty
                            <p class="text-dark-400 text-sm">No social links found</p>
                        @endforelse
                    </div>
                </div>

                {{-- Right — Profile card --}}
                <div class="relative flex justify-center items-center animate-[fade-up_0.8s_0.2s_ease_both]">
                    {{-- Rotating ring --}}
                    <div class="absolute w-80 h-80 lg:w-96 lg:h-96 border border-neon-500/20 rounded-full animate-spin"
                        style="animation-duration: 20s;"></div>
                    <div class="absolute w-72 h-72 lg:w-88 lg:h-88 border border-neon-500/10 rounded-full animate-spin"
                        style="animation-duration: 15s; animation-direction: reverse;"></div>

                    {{-- Profile image wrapper --}}
                    <div class="relative w-64 h-64 lg:w-72 lg:h-72 animate-float">
                        {{-- Glow --}}
                        <div class="absolute inset-0 rounded-3xl"
                            style="background: radial-gradient(circle at center, oklch(0.66 0.17 195 / 0.3) 0%, transparent 70%); filter: blur(20px);">
                        </div>

                        {{-- Image --}}
                        <div class="relative w-full h-full rounded-3xl overflow-hidden glass-strong neon-border">
                            <img src="{{ asset('assets/img/perfil.png') }}" alt="Patrick Moz"
                                class="w-full h-full object-cover object-top">
                        </div>

                        {{-- Floating cards --}}
                        <div
                            class="absolute -bottom-4 -left-8 glass neon-border rounded-2xl px-4 py-3 animate-[fade-up_1s_0.5s_ease_both]">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg gradient-neon flex items-center justify-center">
                                    <i class="ri-code-line text-dark-950 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-dark-100 text-xs font-bold">{{ $settings->years_experience }}</p>
                                    <p class="text-dark-400 text-xs">Experience</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute -top-4 -right-8 glass neon-border rounded-2xl px-4 py-3 animate-[fade-up_1s_0.4s_ease_both]">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg gradient-neon flex items-center justify-center">
                                    <i class="ri-award-line text-dark-950 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-dark-100 text-xs font-bold">{{ $settings->projects_count }}+</p>
                                    <p class="text-dark-400 text-xs">Projects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scroll indicator --}}
            <div class="flex justify-center mt-20 animate-[fade-up_1s_1s_ease_both]">
                <a href="#about"
                    class="flex flex-col items-center gap-2 text-dark-500 hover:text-neon-500 transition-colors group">
                    <span class="text-xs tracking-widest uppercase">Scroll</span>
                    <div
                        class="w-px h-10 bg-gradient-to-b from-neon-500 to-transparent group-hover:h-14 transition-all duration-300">
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== ABOUT ===================== --}}
    <section id="about" class="section-padding relative overflow-hidden">
        <div class="blob"
            style="width:400px;height:400px;background:oklch(0.66 0.17 195 / 0.07);bottom:-100px;right:-80px;filter:blur(80px);position:absolute;border-radius:50%;pointer-events:none;">
        </div>

        <div class="container-custom">
            {{-- Section header --}}
            <div class="text-center mb-16 reveal">
                <span class="text-neon-500 text-sm font-semibold tracking-widest uppercase">About Me</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl mt-2 gradient-text-white">Who I Am
                </h2>
                <p class="text-dark-400 mt-4 max-w-xl mx-auto">{{ $settings->bio }}</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Image side --}}
                <div class="reveal">
                    <div class="relative">
                        {{-- Decorative --}}
                        <div class="absolute -inset-4 rounded-3xl"
                            style="background: linear-gradient(135deg, oklch(0.66 0.17 195 / 0.1), transparent); border-radius: 1.5rem;">
                        </div>
                        <div class="relative rounded-3xl overflow-hidden glass neon-border aspect-[4/5]">
                            <img src="{{ asset('assets/img/perfil.png') }}" alt="Patrick Moz"
                                class="w-full h-full object-cover object-top">
                            {{-- Overlay gradient --}}
                            <div class="absolute inset-0"
                                style="background: linear-gradient(to top, oklch(0.10 0.01 255 / 0.4) 0%, transparent 60%);">
                            </div>
                        </div>

                        {{-- Stats card --}}
                        <div class="absolute -bottom-6 -right-6 glass-strong neon-border rounded-2xl p-5">
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ([[$settings->years_experience . '+', 'Years Exp.'], [$settings->projects_count . '+', 'Projects'], [$settings->clients_count . '+', 'Clients'], ['100%', 'Satisfaction']] as [$num, $label])
                                    <div class="text-center">
                                        <p class="font-display font-bold text-xl text-neon-400">{{ $num }}
                                        </p>
                                        <p class="text-dark-400 text-xs">{{ $label }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Text side --}}
                <div class="space-y-8 reveal">
                    <div class="space-y-4">
                        <h3 class="font-display font-bold text-2xl text-dark-100">
                            {{ $settings->tagline }} &amp;
                            {{-- <span class=text-neon-500">{{ $settings->tagline }}</span> --}}
                        </h3>
                        <p class="text-dark-400 leading-relaxed">
                            {{ $settings->about_me }}
                        </p>
                    </div>

                    {{-- Info grid --}}
                    <div class="grid grid-cols-2 gap-3">

                        {{-- Location --}}
                        <div class="flex items-center gap-3 glass rounded-xl p-3">
                            <div class="w-8 h-8 rounded-lg bg-neon-500/10 flex items-center justify-center shrink-0">
                                <i class="ri-map-pin-line text-neon-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-dark-500 text-xs">Location</p>
                                <p class="text-dark-200 text-sm font-medium">{{ $settings->location }}</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-center gap-3 glass rounded-xl p-3">
                            <div class="w-8 h-8 rounded-lg bg-neon-500/10 flex items-center justify-center shrink-0">
                                <i class="ri-mail-line text-neon-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-dark-500 text-xs">Email</p>
                                <p class="text-dark-200 text-sm font-medium">{{ $settings->email }}</p>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-center gap-3 glass rounded-xl p-3">
                            <div class="w-8 h-8 rounded-lg bg-neon-500/10 flex items-center justify-center shrink-0">
                                <i class="ri-briefcase-line text-neon-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-dark-500 text-xs">Status</p>
                                <p class="text-dark-200 text-sm font-medium">
                                    {{ $settings->available_for_freelance ? 'Available' : 'Not Available' }}</p>
                            </div>
                        </div>

                        {{-- Languages --}}
                        <div class="flex items-center gap-3 glass rounded-xl p-3">
                            <div class="w-8 h-8 rounded-lg bg-neon-500/10 flex items-center justify-center shrink-0">
                                <i class="ri-translate-2 text-neon-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-dark-500 text-xs">Languages</p>
                                <p class="text-dark-200 text-sm font-medium">{{ $languages }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="flex gap-4">
                        <a href="{{ $settings->cv_file }}"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-dark-950 gradient-neon neon-glow hover:scale-105 transition-transform duration-300">
                            <i class="ri-download-line"></i> Download CV
                        </a>
                        <a href="#contact"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-dark-100 glass neon-border hover:text-neon-400 transition-all duration-300">
                            Let's Talk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== SKILLS ===================== --}}
    <section id="skills" class="section-padding relative">
        <div class="container-custom">
            <div class="text-center mb-16 reveal">
                <span class="text-neon-500 text-sm font-semibold tracking-widest uppercase">My Skills</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl mt-2 gradient-text-white">What I
                    Work With</h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-16 items-start">
                {{-- Technical skills --}}
                <div class="space-y-6 reveal">
                    <h3 class="font-display font-semibold text-lg text-dark-200 mb-6">Technical Skills</h3>
                    @foreach ($skills as $skill)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="{{ $skill->icon }} text-neon-500 text-base"></i>
                                    <span class="text-dark-200 font-medium text-sm">{{ $skill->name }}</span>
                                </div>
                                <span class="text-neon-500 text-sm font-semibold">{{ $skill->proficiency }}%</span>
                            </div>
                            <div class="h-1 rounded-full bg-dark-700">
                                <div class="skill-bar-fill" style="width: {{ $skill->proficiency }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Tools & Tech --}}
                <div class="space-y-8 reveal">
                    <h3 class="font-display font-semibold text-lg text-dark-200">Tools & Technologies</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach ($skills as $skill)
                            <div
                                class="group glass rounded-2xl p-3 flex flex-col items-center gap-2 hover:neon-border hover:scale-105 transition-all duration-300 cursor-default">
                                <i class="{{ $skill->icon }} text-2xl" style="color: {{ $skill->color }};"></i>
                                <span
                                    class="text-dark-400 text-xs group-hover:text-dark-200 transition-colors">{{ $skill->name }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Experience timeline --}}
                    <div class="glass rounded-2xl p-6 space-y-4">
                        <h4 class="text-dark-200 font-semibold text-sm">Experience</h4>
                        @foreach ($experiences as $experience)
                            <div class="flex gap-4 items-start">
                                <div class="w-2 h-2 rounded-full bg-neon-500 mt-2 shrink-0"
                                    style="box-shadow: 0 0 8px oklch(0.66 0.17 195 / 0.7);"></div>
                                <div>
                                    <p class="text-dark-200 font-medium text-sm">{{ $experience->job_title }}</p>
                                    <p class="text-dark-400 text-xs">{{ $experience->company }} ·
                                        {{ $experience->start_date }} - {{ $experience->end_date }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== SERVICES ===================== --}}
    <section id="services" class="section-padding relative overflow-hidden">
        <div class="blob"
            style="width:350px;height:350px;background:oklch(0.60 0.15 220 / 0.08);top:50%;left:-80px;transform:translateY(-50%);filter:blur(80px);position:absolute;border-radius:50%;pointer-events:none;">
        </div>

        <div class="container-custom relative z-10">
            <div class="text-center mb-16 reveal">
                <span class="text-neon-500 text-sm font-semibold tracking-widest uppercase">Services</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl mt-2 gradient-text-white">What I Do
                </h2>
                <p class="text-dark-400 mt-4 max-w-xl mx-auto">Comprehensive digital services tailored to your needs.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($services as $service)
                    <div
                        class="group glass rounded-2xl p-6 hover:neon-border hover:scale-[1.02] transition-all duration-300 reveal">
                        <div
                            class="w-12 h-12 rounded-xl gradient-neon flex items-center justify-center mb-4 group-hover:neon-glow transition-all duration-300">
                            <i class="{{ $service->icon }} text-dark-950 text-xl"></i>
                        </div>
                        <h3 class="font-display font-semibold text-dark-100 text-lg mb-2">{{ $service->title }}</h3>
                        <p class="text-dark-400 text-sm leading-relaxed mb-4">{{ $service->description }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($service->tags as $tag)
                                <span
                                    class="text-xs px-2 py-1 rounded-full bg-neon-500/10 text-neon-400 font-medium">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== PROJECTS ===================== --}}
    <section id="projects" class="section-padding relative">
        <div class="container-custom">
            <div class="text-center mb-16 reveal">
                <span class="text-neon-500 text-sm font-semibold tracking-widest uppercase">Portfolio</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl mt-2 gradient-text-white">Recent
                    Projects</h2>
                <p class="text-dark-400 mt-4 max-w-xl mx-auto">A selection of my best work across different industries.
                </p>
            </div>

            {{-- Filter tabs --}}
            <div class="flex justify-center gap-2 mb-10 flex-wrap reveal">
                @foreach (['All', 'Web', 'Mobile', 'Automations', 'Scripts'] as $filter)
                    <button
                        class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $filter === 'All' ? 'gradient-neon text-dark-950' : 'glass text-dark-400 hover:text-dark-100 hover:neon-border' }}"
                        data-filter="{{ $filter }}">
                        {{ $filter }}
                    </button>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" id="projects-grid">
                @foreach ($projects as $project)
                    <article class="project-card group relative rounded-2xl overflow-hidden glass neon-border reveal"
                        data-category="{{ $project->category }}">
                        <div class="overflow-hidden">
                            <img src="{{ !empty($project->images) ? Storage::url($project->images[0]) : '' }}"
                                alt="{{ $project->title }}" class="project-img w-full h-52 object-cover bg-dark-900">
                        </div>
                        <div class="project-overlay absolute inset-0 flex flex-col justify-end p-5">
                            <span
                                class="text-neon-400 text-xs font-semibold uppercase tracking-wider mb-1">{{ $project->category }}</span>
                            <h3 class="text-dark-100 font-display font-bold text-lg">{{ $project->title }}</h3>
                            <p class="text-dark-300 text-xs mt-1 mb-3">{{ $project->description }}</p>
                            <div class="flex items-center gap-3">
                                @if ($project->live_url)
                                    <a href="{{ $project->live_url }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-dark-950 gradient-neon px-3 py-1.5 rounded-full hover:scale-105 transition-transform">
                                        Live Demo <i class="ri-external-link-line"></i>
                                    </a>
                                @endif
                                @if ($project->repo_url)
                                    <a href="{{ $project->repo_url }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-dark-200 glass px-3 py-1.5 rounded-full hover:text-neon-400 transition-colors">
                                        <i class="ri-github-line"></i> Code
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- Tech badge --}}
                        <div class="absolute top-3 right-3 glass rounded-lg px-2 py-1">
                            <span
                                class="text-dark-300 text-xs">{{ is_array($project->tech_stack) ? implode(', ', $project->tech_stack) : $project->tech_stack }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-12 reveal">
                <a href="#"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-dark-100 glass neon-border hover:text-neon-400 hover:neon-glow transition-all duration-300">
                    View All Projects <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== CONTACT ===================== --}}
    <section id="contact" class="section-padding relative overflow-hidden">
        <div class="blob"
            style="width:400px;height:400px;background:oklch(0.66 0.17 195 / 0.08);top:0;right:-80px;filter:blur(80px);position:absolute;border-radius:50%;pointer-events:none;">
        </div>

        <div class="container-custom relative z-10">
            <div class="text-center mb-16 reveal">
                <span class="text-neon-500 text-sm font-semibold tracking-widest uppercase">Contact</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl mt-2 gradient-text-white">Let's Work
                    Together</h2>
                <p class="text-dark-400 mt-4 max-w-xl mx-auto">Have a project in mind? I'd love to hear about it. Let's
                    create something amazing together.</p>
            </div>

            <div class="grid lg:grid-cols-5 gap-12 items-start max-w-5xl mx-auto">
                {{-- Contact info --}}
                <div class="lg:col-span-2 space-y-6 reveal">
                    <a href="mailto:{{ $settings->email }}" target="_blank" type="email"
                        class="flex items-center gap-4 glass rounded-2xl p-4 group hover:neon-border transition-all duration-300">
                        <div
                            class="w-11 h-11 rounded-xl gradient-neon flex items-center justify-center shrink-0 group-hover:neon-glow transition-all">
                            <i class="ri-mail-send-line text-dark-950 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-dark-500 text-xs">Email</p>
                            <p class="text-dark-100 font-medium text-sm">{{ $settings->email }}</p>
                        </div>
                    </a>
                    <a href="{{ $settings->phone }}" target="_blank" type="tel"
                        class="flex items-center gap-4 glass rounded-2xl p-4 group hover:neon-border transition-all duration-300">
                        <div
                            class="w-11 h-11 rounded-xl gradient-neon flex items-center justify-center shrink-0 group-hover:neon-glow transition-all">
                            <i class="ri-phone-line text-dark-950 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-dark-500 text-xs">Phone</p>
                            <p class="text-dark-100 font-medium text-sm">{{ $settings->phone }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-4 glass rounded-2xl p-4 group">
                        <div class="w-11 h-11 rounded-xl gradient-neon flex items-center justify-center shrink-0">
                            <i class="ri-map-pin-2-line text-dark-950 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-dark-500 text-xs">Location</p>
                            <p class="text-dark-100 font-medium text-sm">{{ $settings->location }}</p>
                        </div>
                    </div>

                    {{-- Socials --}}
                    <div class="glass rounded-2xl p-5">
                        <p class="text-dark-400 text-sm mb-4">Follow me on</p>
                        <div class="flex gap-3">
                            @foreach ($social_links as $link)
                                <a href="{{ $link['url'] }}" target="_blank"
                                    class="flex-1 flex flex-col items-center gap-1 glass rounded-xl p-3 text-dark-400 hover:text-neon-500 hover:neon-border transition-all duration-300 group">
                                    <i class="{{ $link['icon'] }} text-xl"></i>
                                    @if (isset($link['name']))
                                        <span class="text-xs">{{ $link['name'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="lg:col-span-3 reveal">
                    <form id="contact-form" class="glass-strong rounded-2xl p-8 space-y-5">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="text-dark-300 text-sm font-medium">Your Name</label>
                                <input type="text" placeholder="John Doe"
                                    class="w-full bg-dark-800 border border-dark-600 rounded-xl px-4 py-3 text-dark-100 text-sm placeholder:text-dark-600 focus:outline-none focus:border-neon-500/60 focus:ring-1 focus:ring-neon-500/30 transition-all duration-300">
                            </div>
                            <div class="space-y-2">
                                <label class="text-dark-300 text-sm font-medium">Email Address</label>
                                <input type="email" placeholder="john@example.com"
                                    class="w-full bg-dark-800 border border-dark-600 rounded-xl px-4 py-3 text-dark-100 text-sm placeholder:text-dark-600 focus:outline-none focus:border-neon-500/60 focus:ring-1 focus:ring-neon-500/30 transition-all duration-300">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-dark-300 text-sm font-medium">Subject</label>
                            <input type="text" placeholder="Project inquiry"
                                class="w-full bg-dark-800 border border-dark-600 rounded-xl px-4 py-3 text-dark-100 text-sm placeholder:text-dark-600 focus:outline-none focus:border-neon-500/60 focus:ring-1 focus:ring-neon-500/30 transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="text-dark-300 text-sm font-medium">Message</label>
                            <textarea rows="5" placeholder="Tell me about your project..."
                                class="w-full bg-dark-800 border border-dark-600 rounded-xl px-4 py-3 text-dark-100 text-sm placeholder:text-dark-600 focus:outline-none focus:border-neon-500/60 focus:ring-1 focus:ring-neon-500/30 transition-all duration-300 resize-none"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl font-semibold text-dark-950 gradient-neon neon-glow hover:scale-[1.02] transition-transform duration-300">
                            Send Message <i class="ri-send-plane-fill text-base"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="border-t border-dark-700 py-10">
        <div class="container-custom">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <a href="#home" class="font-display font-bold text-xl text-dark-100">
                    {{ $settings->url_prefix }}<span class="text-neon-500">.{{ $settings->url_suffix }}</span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="#home" class="text-dark-400 hover:text-neon-500 text-sm transition-colors">Home</a>
                    <a href="#about" class="text-dark-400 hover:text-neon-500 text-sm transition-colors">About</a>
                    <a href="#projects"
                        class="text-dark-400 hover:text-neon-500 text-sm transition-colors">Projects</a>
                    <a href="#contact" class="text-dark-400 hover:text-neon-500 text-sm transition-colors">Contact</a>
                </div>
                <p class="text-dark-500 text-sm">&copy; {{ date('Y') }} Mohamed Zomlot. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- ===================== SCROLL UP ===================== --}}
    <a href="#home" id="scroll-up"
        class="fixed bottom-6 right-6 w-11 h-11 rounded-xl gradient-neon neon-glow flex items-center justify-center text-dark-950 font-bold opacity-0 pointer-events-none transition-all duration-300 z-40 hover:scale-110">
        <i class="ri-arrow-up-line text-lg"></i>
    </a>

    {{-- ===================== SCRIPTS ===================== --}}
    <script>
        (function() {
            /* ── Navbar scroll effect ── */
            const navbar = document.getElementById('navbar');
            const onScroll = () => {
                if (window.scrollY > 40) {
                    navbar.style.cssText =
                        'background:oklch(0.13 0.01 255 / 0.85);backdrop-filter:blur(24px);border-bottom:1px solid oklch(0.66 0.17 195 / 0.1);';
                } else {
                    navbar.style.cssText = '';
                }
            };
            window.addEventListener('scroll', onScroll, {
                passive: true
            });

            /* ── Scroll-up button ── */
            const scrollUp = document.getElementById('scroll-up');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) {
                    scrollUp.style.opacity = '1';
                    scrollUp.style.pointerEvents = 'auto';
                } else {
                    scrollUp.style.opacity = '0';
                    scrollUp.style.pointerEvents = 'none';
                }
            }, {
                passive: true
            });

            /* ── Mobile menu ── */
            const hamburger = document.getElementById('hamburger');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const mobileMenu = document.getElementById('mobile-menu');
            hamburger.addEventListener('click', () => {
                const isOpen = mobileMenu.classList.toggle('open');
                hamburgerIcon.className = isOpen ? 'ri-close-line text-2xl text-neon-500' :
                    'ri-menu-3-line text-2xl';
            });
            mobileMenu.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    hamburgerIcon.className = 'ri-menu-3-line text-2xl';
                });
            });

            /* ── Active nav link ── */
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
                        if (active) active.classList.add('active');
                    }
                });
            }, {
                threshold: 0.4
            });
            sections.forEach(s => observer.observe(s));

            /* ── Reveal on scroll ── */
            const revealObs = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        revealObs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -60px 0px'
            });
            document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

            /* ── Project filter ── */
            const filterBtns = document.querySelectorAll('.filter-btn');
            const projectCards = document.querySelectorAll('#projects-grid article');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const filter = btn.dataset.filter;
                    filterBtns.forEach(b => {
                        b.className = b.className.replace('gradient-neon text-dark-950',
                            'glass text-dark-400 hover:text-dark-100 hover:neon-border');
                    });
                    btn.className = btn.className.replace(
                        'glass text-dark-400 hover:text-dark-100 hover:neon-border',
                        'gradient-neon text-dark-950');
                    projectCards.forEach(card => {
                        const show = filter === 'All' || card.dataset.category === filter;
                        card.style.display = show ? '' : 'none';
                    });
                });
            });

            /* ── Smooth contact form ── */
            const form = document.getElementById('contact-form');
            if (form) {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const btn = form.querySelector('button[type=submit]');
                    btn.innerHTML = '<i class="ri-check-line text-base"></i> Message Sent!';
                    btn.disabled = true;
                    setTimeout(() => {
                        btn.innerHTML = 'Send Message <i class="ri-send-plane-fill text-base"></i>';
                        btn.disabled = false;
                        form.reset();
                    }, 3000);
                });
            }
        })();
    </script>
</body>

</html>
