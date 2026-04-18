<header id="navbar" class="fixed top-0 inset-x-0 z-50 transition-all duration-500 py-4">
    <div class="container mx-auto px-4 md:px-6">
        <nav class="flex items-center justify-between h-16 md:h-20 px-6 rounded-2xl bg-dark-900/50 backdrop-blur-xl border border-dark-600/50 shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.1)]">
            {{-- Logo --}}
            <a href="#home" class="font-display font-bold text-xl text-dark-100 tracking-tight group flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-neon-500 flex items-center justify-center text-dark-950 shadow-[0_0_15px_oklch(0.66_0.17_195_/_0.3)] group-hover:scale-110 transition-transform">
                    <i class="ri-code-s-slash-line font-bold"></i>
                </div>
                <span>
                    {{ $settings->first_name ?? 'Patrick' }}<span class="text-neon-500 group-hover:text-neon-400 transition-colors">.{{ $settings->last_name ?? 'cms' }}</span>
                </span>
            </a>

            {{-- Desktop Links --}}
            <ul class="hidden md:flex items-center gap-8">
                @foreach ([['#home', 'Home'], ['#about', 'About'], ['#skills', 'Skills'], ['#services', 'Services'], ['#projects', 'Projects'], ['#contact', 'Contact']] as [$href, $label])
                    <li>
                        <a href="{{ $href }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-dark-400 hover:text-neon-400 transition-all relative group py-2">
                            {{ $label }}
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-neon-500 transition-all duration-300 group-hover:w-full shadow-[0_0_8px_oklch(0.66_0.17_195_/_0.6)]"></span>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- CTA + Hamburger --}}
            <div class="flex items-center gap-4">
                <x-button variant="neon" size="sm" href="#contact" class="hidden md:inline-flex group">
                    Hire Me <i class="ri-arrow-right-up-line group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                </x-button>
                
                <button id="hamburger" class="md:hidden text-dark-300 hover:text-neon-500 transition-colors p-2 rounded-xl bg-dark-800/50 border border-dark-600/50">
                    <i id="hamburger-icon" class="ri-menu-3-line text-xl"></i>
                </button>
            </div>
        </nav>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden flex-col gap-1 mt-2 p-2 rounded-2xl bg-dark-900/95 backdrop-blur-xl border border-dark-600/50 md:hidden animate-fade-in">
            @foreach ([['#home', 'Home'], ['#about', 'About'], ['#skills', 'Skills'], ['#services', 'Services'], ['#projects', 'Projects'], ['#contact', 'Contact']] as [$href, $label])
                <a href="{{ $href }}" class="block px-4 py-3 rounded-xl text-dark-300 hover:text-neon-400 hover:bg-neon-500/5 font-bold uppercase tracking-widest text-[10px] transition-all">{{ $label }}</a>
            @endforeach
        </div>
    </div>
</header>

@push('scripts')
<script>
    const navbarElement = document.getElementById('navbar');
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbarElement.classList.add('py-2');
            navbarElement.classList.remove('py-4');
        } else {
            navbarElement.classList.add('py-4');
            navbarElement.classList.remove('py-2');
        }
    });

    hamburger?.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('flex');
        hamburgerIcon.className = mobileMenu.classList.contains('hidden') ? 'ri-menu-3-line text-xl' : 'ri-close-line text-xl';
    });

    document.querySelectorAll('#mobile-menu a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            mobileMenu.classList.remove('flex');
            hamburgerIcon.className = 'ri-menu-3-line text-xl';
        });
    });
</script>
@endpush
