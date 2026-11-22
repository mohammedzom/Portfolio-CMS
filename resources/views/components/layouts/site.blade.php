@props([
    'title',
    'description' => null,
    'image' => null,
    'person' => [],
])

@php
    $canonical = url('/');
    $sameAs = collect($person['social'] ?? [])->pluck('url')->values()->all();
    $structuredData = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $person['name'] ?? null,
        'jobTitle' => $person['role'] ?? null,
        'description' => $description,
        'url' => $canonical,
        'image' => $image,
        'sameAs' => $sameAs ?: null,
        'address' => filled($person['location'] ?? null) ? ['@type' => 'PostalAddress', 'addressLocality' => $person['location']] : null,
    ]);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif
    <meta name="theme-color" content="#0b0b0d">
    <meta name="color-scheme" content="dark">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="{{ $person['name'] ?? config('app.name') }}">
    <meta property="og:title" content="{{ $title }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:url" content="{{ $canonical }}">
    @if ($image)
        <meta property="og:image" content="{{ $image }}">
        <meta property="og:image:alt" content="Portrait of {{ $person['name'] ?? '' }}">
    @endif
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title }}">
    @if ($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif

    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

    {{-- Animation start states only apply once we know JavaScript is running. --}}
    <script>document.documentElement.classList.add('js');</script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="progress" aria-hidden="true"><span class="progress__bar" data-scroll-progress></span></div>

    {{ $slot }}

    <div class="toast" role="status" aria-live="polite" x-data x-text="$store.toast.message" :class="{ 'is-on': $store.toast.visible }"></div>
</body>
</html>
