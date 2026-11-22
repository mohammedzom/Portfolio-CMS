@php
    $person = $page['person'];
@endphp

<section class="section" id="about" aria-labelledby="about-title">
    <div class="wrap about">
        <div class="about__media" data-reveal>
            <div class="avatar">
                {{-- The monogram sits behind the photo and shows if the photo is missing or fails to load. --}}
                <div class="avatar__fallback" aria-hidden="true">{{ $person['initials'] ?: 'MZ' }}</div>
                @if ($person['avatar'])
                    <img src="{{ $person['avatar'] }}" alt="Portrait of {{ $person['name'] }}" width="480" height="600" loading="lazy" decoding="async" data-fallback-image>
                @endif
            </div>

            @if ($page['facts'] !== [])
                <dl class="about__facts">
                    @foreach ($page['facts'] as $fact)
                        <div><dt>{{ $fact['label'] }}</dt><dd>{{ $fact['value'] }}</dd></div>
                    @endforeach
                </dl>
            @endif
        </div>

        <div class="about__text" data-reveal>
            <x-site.route path="/about" />
            <h2 class="about__title" id="about-title">Most of what I build, you never see. That’s the point.</h2>
            @if ($person['about'] !== [])
                <div class="about__body">
                    @foreach ($person['about'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
