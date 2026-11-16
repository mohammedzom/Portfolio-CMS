@props([
    'title',
    'description' => null,
    'image' => null,
    'person' => [],
])

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
    <link rel="canonical" href="{{ url('/') }}">

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
