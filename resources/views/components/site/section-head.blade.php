@props(['path', 'titleId', 'verb' => 'GET', 'status' => 200])

<header {{ $attributes->class('sec-head') }} data-reveal>
    <x-site.route :verb="$verb" :path="$path" :status="$status" />
    <h2 class="sec-title" id="{{ $titleId }}">{{ $title }}</h2>
    @isset($subtitle)
        <p class="sec-sub">{{ $subtitle }}</p>
    @endisset
</header>
