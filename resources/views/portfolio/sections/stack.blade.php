@php
    $titles = collect($page['projects'])->pluck('title', 'slug');
@endphp

<section class="section" id="stack" aria-labelledby="stack-title" x-data="stackUsage">
    <div class="wrap">
        <x-site.section-head path="/stack" title-id="stack-title">
            <x-slot:title>Technical stack</x-slot:title>
            <x-slot:subtitle>Select a technology to see which projects on this page use it.</x-slot:subtitle>
        </x-site.section-head>

        <div class="stack">
            @foreach ($page['stack'] as $group)
                <div class="stack__group" data-reveal style="--d: {{ $loop->index * 0.07 }}s">
                    <h3>{{ $group['group'] }} <span>{{ count($group['items']) }}</span></h3>
                    <div class="stack__list">
                        @foreach ($group['items'] as $item)
                            @php($used = count($item['projects']))
                            <button
                                type="button"
                                class="tech"
                                aria-pressed="false"
                                :aria-pressed="(selected === @js($item['name'])).toString()"
                                @click="toggle(@js($item['name']), @js($item['projects']))"
                            >
                                <span class="tech__glyph" aria-hidden="true">{{ $item['glyph'] }}</span>{{ $item['name'] }}
                                @if ($used)
                                    <span class="tech__n"><span aria-hidden="true">{{ $used }}</span><span class="sr-only">, used in {{ $used }} {{ \Illuminate\Support\Str::plural('project', $used) }}</span></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="stack__usage" aria-live="polite">
            <template x-if="selected === null">
                <p class="stack__usage-empty">Nothing selected yet.</p>
            </template>
            <template x-if="selected !== null && projects.length">
                <p><strong x-text="selected"></strong> appears in <span x-text="projects.length === 1 ? '1 project' : projects.length + ' projects'"></span>:</p>
            </template>
            <template x-for="slug in projects" :key="slug">
                <button type="button" aria-haspopup="dialog" @click="$dispatch('open-project', { slug, trigger: $el })" x-text="titles[slug] ?? slug"></button>
            </template>
            <template x-if="selected !== null && ! projects.length">
                <p><strong x-text="selected"></strong> is part of my everyday toolkit. None of the projects above lists it as a headline technology.</p>
            </template>
        </div>
        <script type="application/json" data-project-titles>{!! json_encode($titles, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    </div>
</section>
