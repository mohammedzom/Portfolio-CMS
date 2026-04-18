<footer class="border-t border-dark-600/50 py-12 relative z-10 bg-dark-950/80 backdrop-blur-sm">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-center">
            {{-- Logo --}}
            <div class="flex flex-col gap-4">
                <a href="#home" class="font-display font-bold text-2xl text-dark-100 tracking-tight group flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-neon-500 flex items-center justify-center text-dark-950 shadow-[0_0_15px_oklch(0.66_0.17_195_/_0.3)] group-hover:scale-110 transition-transform">
                        <i class="ri-code-s-slash-line font-bold"></i>
                    </div>
                    <span>
                        {{ $settings->first_name ?? 'Patrick' }}<span class="text-neon-500 group-hover:text-neon-400 transition-colors">.{{ $settings->last_name ?? 'cms' }}</span>
                    </span>
                </a>
                <p class="text-dark-500 text-xs font-bold uppercase tracking-widest max-w-xs">
                    Building modern, responsive, and high-performance digital experiences.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                @foreach(['Home' => '#home', 'About' => '#about', 'Projects' => '#projects', 'Contact' => '#contact'] as $label => $href)
                    <a href="{{ $href }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-dark-400 hover:text-neon-400 transition-colors">{{ $label }}</a>
                @endforeach
            </div>

            {{-- Social & Copyright --}}
            <div class="flex flex-col items-center md:items-end gap-6">
                <div class="flex items-center gap-4">
                    @foreach ($social_links ?? [] as $link)
                        <a href="{{ $link->url }}" target="_blank" class="w-10 h-10 rounded-xl bg-dark-900 border border-dark-600/50 flex items-center justify-center text-dark-400 hover:text-neon-500 hover:border-neon-500/40 hover:scale-110 transition-all duration-300 shadow-sm">
                            <i class="{{ $link->icon ?? 'ri-link' }} text-lg"></i>
                        </a>
                    @endforeach
                </div>
                <p class="text-dark-600 text-[10px] font-black uppercase tracking-[0.2em]">
                    &copy; {{ date('Y') }} {{ $settings->first_name ?? 'Mohamed' }} {{ $settings->last_name ?? 'Zomlot' }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

{{-- Scroll to top --}}
<button id="scroll-to-top" class="fixed bottom-8 right-8 w-12 h-12 rounded-2xl bg-neon-500 text-dark-950 shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.4)] flex items-center justify-center opacity-0 pointer-events-none translate-y-10 transition-all duration-500 z-50 hover:scale-110 hover:bg-neon-400">
    <i class="ri-arrow-up-line text-xl font-bold"></i>
</button>

@push('scripts')
<script>
    const scrollToTopBtn = document.getElementById('scroll-to-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-10');
        } else {
            scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-10');
        }
    });

    scrollToTopBtn?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
@endpush
