@php
    $expertise = $page['expertise'];
    $titles = collect($page['projects'])->pluck('title', 'slug');
    $count = count($expertise);
@endphp

<section class="section section--alt" id="expertise" aria-labelledby="expertise-title">
    <div class="wrap">
        <x-site.section-head path="/expertise" title-id="expertise-title">
            <x-slot:title>How I work, layer by layer</x-slot:title>
            <x-slot:subtitle>{{ $count === 1 ? 'My specialisation' : ucfirst(\Illuminate\Support\Number::spell($count)).' specialisations' }}, each shown as the pipeline I build — not as a skill percentage.</x-slot:subtitle>
        </x-site.section-head>

        <div class="exp" x-data="expertiseTabs" data-reveal>
            <div class="exp__tabs" role="tablist" aria-label="Specialisations" aria-orientation="vertical" @keydown="onKeydown($event)">
                @foreach ($expertise as $item)
                    <button
                        type="button"
                        class="exp__tab"
                        role="tab"
                        id="tab-{{ $item['key'] }}"
                        aria-controls="panel-{{ $item['key'] }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        tabindex="{{ $loop->first ? '0' : '-1' }}"
                        data-tab="{{ $item['key'] }}"
                        :aria-selected="(active === @js($item['key'])).toString()"
                        :tabindex="active === @js($item['key']) ? 0 : -1"
                        @click="select(@js($item['key']))"
                    >
                        <span class="exp__tab-name">{{ $item['title'] }}</span>
                        @if ($item['summary'] !== '')
                            <span class="exp__tab-sum">{{ $item['summary'] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            @foreach ($expertise as $item)
                <div
                    class="exp__panel"
                    id="panel-{{ $item['key'] }}"
                    role="tabpanel"
                    aria-labelledby="tab-{{ $item['key'] }}"
                    tabindex="0"
                    @unless ($loop->first) hidden @endunless
                    :hidden="active !== @js($item['key'])"
                    data-panel="{{ $item['key'] }}"
                >
                    <div class="exp__panel-in">
                        <h3>{{ $item['title'] }}</h3>
                        @foreach ($item['description'] as $paragraph)
                            <p class="exp__panel-text">{{ $paragraph }}</p>
                        @endforeach

                        @if ($item['steps'] !== [])
                            <ol class="flow" aria-label="Pipeline" style="--steps: {{ count($item['steps']) }}">
                                @if (count($item['steps']) > 1)
                                    <li class="flow__dot" aria-hidden="true" x-show="animated"></li>
                                @endif
                                @foreach ($item['steps'] as $step)
                                    <li class="flow__step" data-step="{{ $loop->index }}" :class="{ 'is-lit': active === @js($item['key']) && lit === {{ $loop->index }} }">
                                        <span class="flow__num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div><span class="flow__name">{{ $step }}</span></div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif

                        @if ($item['proof'] !== [])
                            <div class="proof">
                                <span class="proof__label">Seen in</span>
                                @foreach ($item['proof'] as $slug)
                                    <button type="button" aria-haspopup="dialog" @click="$dispatch('open-project', { slug: @js($slug), trigger: $el })">{{ $titles[$slug] ?? $slug }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
