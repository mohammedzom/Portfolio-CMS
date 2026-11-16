<section class="section section--alt" id="log" aria-labelledby="log-title">
    <div class="wrap">
        <div class="log-grid">
            <x-site.section-head path="/log" title-id="log-title">
                <x-slot:title>Experience &amp; achievements</x-slot:title>
                <x-slot:subtitle>Education, work experience, programming contests and certifications.</x-slot:subtitle>
            </x-site.section-head>

            <div class="timeline" data-timeline>
                <span class="timeline__fill" aria-hidden="true" data-timeline-fill></span>
                <ol class="timeline__list">
                @foreach ($page['timeline'] as $entry)
                    <li class="tl tl--{{ $entry['kind'] }}" data-reveal>
                        <span class="tl__mark" aria-hidden="true"></span>
                        <p class="tl__when">{{ $entry['when'] }}</p>
                        <div>
                            <h3 class="tl__title">{{ $entry['title'] }}@if ($entry['kind'] === 'award')<span class="tl__badge">award</span>@endif</h3>
                            @if ($entry['org'])
                                <p class="tl__org">{{ $entry['org'] }}</p>
                            @endif
                            @if ($entry['body'])
                                <p class="tl__body">{{ $entry['body'] }}</p>
                            @endif
                            @if ($entry['links'] !== [])
                                <p class="tl__links">
                                    @foreach ($entry['links'] as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ $link['label'] }}<span class="sr-only"> for {{ $entry['title'] }} (opens in a new tab)</span></a>
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
