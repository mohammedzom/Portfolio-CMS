@php
    $person = $page['person'];
    $title = collect([$person['name'], $person['role']])->filter()->implode(' — ') ?: config('app.name');
@endphp

<x-layouts.site :title="$title" :description="$person['bio']" :image="$person['avatar']" :person="$person">
    <main id="main">
        {{-- Sections are added in the following Phase 2 tasks. --}}
    </main>
</x-layouts.site>
