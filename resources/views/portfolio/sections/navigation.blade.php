@php
    $wordmark = $page['person']['wordmark'];
@endphp

<header class="nav" id="top" x-data="siteNavigation" :class="{ 'is-open': open }" @keydown.escape.window="close(true)" @resize.window.debounce.150ms="closeOnDesktop()">
    <div class="nav__inner">
        <a class="wordmark" href="#hero" aria-label="{{ $wordmark['prefix'] }}{{ $wordmark['suffix'] ? '.'.$wordmark['suffix'] : '' }} — back to top">
            {{ $wordmark['prefix'] }}@if ($wordmark['suffix'])<span class="wordmark__dot">.</span>{{ $wordmark['suffix'] }}@endif
        </a>

        @if ($sections !== [])
            <nav class="nav__links" aria-label="Sections">
                @foreach ($sections as $section)
                    <a href="#{{ $section }}" data-nav="{{ $section }}">/{{ $section }}</a>
                @endforeach
            </nav>

            <button type="button" class="nav__menu" aria-controls="mobile-menu" aria-expanded="false" :aria-expanded="open.toString()" x-ref="toggle" @click="toggle()">
                <span class="sr-only" x-text="open ? 'Close menu' : 'Open menu'">Open menu</span>
                <span class="nav__menu-bars" aria-hidden="true"></span>
            </button>
        @endif
    </div>

    @if ($sections !== [])
        <nav class="mobile-menu" id="mobile-menu" aria-label="Sections" hidden :hidden="! open" x-ref="menu" @click="if ($event.target.closest('a')) close()">
            @foreach ($sections as $section)
                <a href="#{{ $section }}" data-nav="{{ $section }}">/{{ $section }}</a>
            @endforeach
        </nav>
    @endif
</header>
