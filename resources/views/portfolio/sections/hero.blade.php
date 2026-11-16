@php
    $person = $page['person'];

    // The request path is a fixed illustration of how the author deploys a Laravel API.
    $nodes = [
        'client' => ['Client', 'A browser, mobile app or Telegram — anything that calls the API over HTTPS with a Sanctum token.'],
        'nginx' => ['Nginx — gateway', 'Terminates TLS, serves static files and proxies API traffic to PHP-FPM. The first line of defence and the first place to look in the logs.'],
        'laravel' => ['Laravel API', 'Routing, validation, authentication and business logic. Responses are shaped with API resources and covered by automated tests.'],
        'redis' => ['Redis — cache & queue', 'Caches read-heavy responses and holds queued jobs such as notifications and scheduled reminders.'],
        'mysql' => ['MySQL — database', 'The source of truth. Schemas are designed with relations, indexes and migrations kept in version control.'],
        'host' => ['Linux VPS · Docker', 'The machine everything runs on: Linux, containers where they help, and Supervisor or systemd keeping workers alive.'],
        'ci' => ['GitHub Actions — deploy', 'Every push runs the test suite. When it passes, the pipeline ships the new release to the server.'],
    ];

    $node = fn (string $key): string => 'data-node="'.$key.'" data-note-title="'.e($nodes[$key][0]).'" data-note-body="'.e($nodes[$key][1]).'" aria-describedby="arch-info"';
@endphp

<section class="hero" id="hero" aria-labelledby="hero-title">
    <div class="hero__grid" aria-hidden="true"></div>
    <div class="hero__inner wrap">
        <div class="hero__head">
            @if ($person['name'])
                <p class="hero__who" data-intro><span class="pulse" aria-hidden="true"></span>{{ collect([$person['name'], $person['role']])->filter()->implode(' — ') }}</p>
            @endif
            <h1 class="hero__title" id="hero-title">
                <span class="line"><span>Engineering what happens</span></span>
                <span class="line"><span>behind the scenes.</span></span>
            </h1>
        </div>

        <div class="hero__copy">
            @if ($person['bio'])
                <p class="hero__lede" data-intro>{{ $person['bio'] }}</p>
            @endif

            @if ($page['focus'] !== [])
                <ul class="hero__focus" aria-label="Focus areas" data-intro>
                    @foreach ($page['focus'] as $focus)
                        <li>{{ $focus }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="hero__ctas" data-intro>
                <a class="btn btn--primary" href="#work">View selected work</a>
                @if ($person['github'])
                    <a class="btn btn--ghost" href="{{ $person['github'] }}" target="_blank" rel="noopener">GitHub profile</a>
                @endif
                <a class="btn btn--text" href="#contact">Get in touch</a>
            </div>
        </div>

        {{-- Interactive architecture: hover or focus a node, or trace a request through the stack. --}}
        <figure class="arch" aria-labelledby="arch-caption" x-data="architectureTrace" data-intro>
            <div class="arch__bar">
                <span class="arch__title" id="arch-caption">Request path of a typical Laravel API I deploy</span>
                <span class="arch__state" :data-state="state" data-state="idle"><span class="arch__led" aria-hidden="true"></span><span x-text="stateText">idle</span></span>
            </div>

            <div class="arch__stage" x-ref="stage" @mouseleave="restoreSelection()">
                <svg class="arch__wires" x-ref="wires" aria-hidden="true"></svg>

                <button type="button" class="node node--ci" style="grid-area: ci" {!! $node('ci') !!}>
                    <span class="node__kind">deploy</span><span class="node__name">GitHub Actions</span>
                </button>
                <button type="button" class="node node--client" style="grid-area: client" {!! $node('client') !!}>
                    <span class="node__kind">client</span><span class="node__name">Browser / App</span>
                </button>

                <div class="host" style="grid-area: host">
                    <button type="button" class="host__label" {!! $node('host') !!}><span class="node__kind">host</span> Linux VPS · Docker</button>
                    <div class="host__inner">
                        <button type="button" class="node" style="grid-area: nginx" {!! $node('nginx') !!}>
                            <span class="node__kind">gateway</span><span class="node__name">Nginx</span>
                        </button>
                        <button type="button" class="node node--core" style="grid-area: laravel" {!! $node('laravel') !!}>
                            <span class="node__kind">application</span><span class="node__name">Laravel API</span>
                        </button>
                        <button type="button" class="node" style="grid-area: redis" {!! $node('redis') !!}>
                            <span class="node__kind">cache · queue</span><span class="node__name">Redis</span>
                        </button>
                        <button type="button" class="node" style="grid-area: mysql" {!! $node('mysql') !!}>
                            <span class="node__kind">database</span><span class="node__name">MySQL</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="arch__panel">
                <div class="arch__info" id="arch-info" aria-live="polite" x-ref="info">
                    <p class="arch__info-title">Select any part of the system</p>
                    <p class="arch__info-body">Hover or tab to a node to see what it does, or trace a request through the stack.</p>
                </div>
                <div class="arch__log" role="log" aria-label="Simulated request trace" tabindex="0" x-ref="log">
                    <span class="arch__log-empty">$ trace will print here</span>
                </div>
            </div>

            <div class="arch__actions">
                <button type="button" class="btn btn--small btn--primary" :disabled="busy" @click="trace()">Trace a request</button>
                <button type="button" class="btn btn--small btn--ghost" x-show="canDeploy" x-cloak :disabled="busy" @click="deploy()">Ship a deploy</button>
                <span class="arch__hint" x-text="hint">First request misses the cache. Run it twice.</span>
            </div>
        </figure>
    </div>
</section>
