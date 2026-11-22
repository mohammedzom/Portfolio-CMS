@props(['project', 'index' => 0])

@php
    $tagLimit = in_array($project['size'], ['std', 'wide', 'half'], true) ? 4 : 6;
@endphp

{{--
    The card is an <article>; its title holds the real button so the accessible
    name stays short. A click anywhere on the card opens the same dialog.
--}}
<article
    class="card card--{{ $project['size'] }}"
    data-project="{{ $project['slug'] }}"
    data-category="{{ $project['categoryKey'] }}"
    data-reveal
    style="--d: {{ ($index % 3) * 0.08 }}s"
    @click="open(@js($project['slug']), $event)"
>
    <div class="card__media">
        <x-site.pattern :type="$project['pattern']" :label="'Abstract illustration for '.$project['title']" />
        @if ($project['images'] !== [])
            <img src="{{ $project['images'][0] }}" alt="Screenshot of {{ $project['title'] }}" loading="lazy" decoding="async" data-fallback-image>
        @endif
    </div>

    <div class="card__body">
        <p class="card__cat">{{ $project['category'] }}</p>
        <h3 class="card__title">
            <button type="button" class="card__trigger" aria-haspopup="dialog" data-open-project="{{ $project['slug'] }}">{{ $project['title'] }}</button>
        </h3>
        @if ($project['summary'] !== '')
            <p class="card__sum">{{ $project['summary'] }}</p>
        @endif
        <div class="card__foot">
            @if ($project['tags'] !== [])
                <ul class="tags" aria-label="Technologies">
                    @foreach (array_slice($project['tags'], 0, $tagLimit) as $tag)
                        <li class="tag">{{ $tag }}</li>
                    @endforeach
                </ul>
            @endif
            <span class="card__open" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
        </div>
    </div>
</article>
