@php
    $person = $page['person'];
    $links = collect($person['social'])
        ->map(fn (array $link): array => ['label' => $link['label'], 'value' => $link['display'], 'url' => $link['url'], 'download' => false])
        ->when($person['cv'], fn ($links) => $links->push(['label' => 'Résumé', 'value' => 'Download CV (PDF)', 'url' => $person['cv'], 'download' => true]))
        ->values();
@endphp

<section class="contact" id="contact" aria-labelledby="contact-title">
    <div class="contact__glow" aria-hidden="true"></div>
    <div class="wrap">
        <x-site.route verb="POST" path="/contact" status="202" data-reveal />
        <h2 class="contact__title" id="contact-title" data-reveal>Have a system that needs building — or keeping alive?</h2>
        <p class="contact__sub" data-reveal>
            {{ $person['email'] ? 'Email works best for anything detailed.' : 'The links below are the best way to reach me.' }}
            @if ($person['email'] && $person['telegram'])
                For a quick question, Telegram is fine too.
            @endif
        </p>

        @if ($person['email'])
            <div class="contact__mail" data-reveal x-data="copyEmail(@js($person['email']))">
                <a class="contact__email" href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a>
                <button type="button" class="btn btn--ghost btn--small" @click="copy()">Copy email</button>
            </div>
        @endif

        @if ($links->isNotEmpty())
            <ul class="contact__links" style="--links: {{ min($links->count(), 4) }}" data-reveal>
                @foreach ($links as $link)
                    <li>
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" @if ($link['download']) download @endif>
                            <span class="contact__k">{{ $link['label'] }}</span>
                            <span class="contact__v">{{ $link['value'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <footer class="footer wrap">
        <span>© {{ now()->year }} {{ $person['name'] }}</span>
        <span class="footer__mono">Served by Portfolio CMS · Laravel</span>
        <a href="#hero">Back to top</a>
    </footer>
</section>
