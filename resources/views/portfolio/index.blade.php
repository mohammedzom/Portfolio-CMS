@php
    $person = $page['person'];
    $title = collect([$person['name'], $person['role']])->filter()->implode(' — ') ?: config('app.name');

    // Sections without CMS content are left out of the page and the navigation.
    $sections = [];
@endphp

<x-layouts.site :title="$title" :description="$person['bio']" :image="$person['avatar']" :person="$person">
    @include('portfolio.sections.navigation', ['sections' => $sections])

    <main id="main">
        @include('portfolio.sections.hero')
    </main>
</x-layouts.site>
