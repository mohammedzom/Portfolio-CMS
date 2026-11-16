@php
    $person = $page['person'];
    $title = collect([$person['name'], $person['role']])->filter()->implode(' — ') ?: config('app.name');

    // Sections without CMS content are left out of the page and the navigation.
    $sections = collect([
        'work' => true,
        'expertise' => $page['expertise'] !== [],
        'stack' => $page['stack'] !== [],
        'log' => $page['timeline'] !== [],
        'about' => $person['about'] !== [] || $page['facts'] !== [] || filled($person['avatar']),
        'contact' => true,
    ])->filter()->keys()->all();
@endphp

<x-layouts.site :title="$title" :description="$person['bio']" :image="$person['avatar']" :person="$person">
    @include('portfolio.sections.navigation', ['sections' => $sections])

    <main id="main">
        @include('portfolio.sections.hero')
        @include('portfolio.sections.projects')

        @if (in_array('expertise', $sections, true))
            @include('portfolio.sections.expertise')
        @endif

        @if (in_array('stack', $sections, true))
            @include('portfolio.sections.stack')
        @endif

        @if (in_array('log', $sections, true))
            @include('portfolio.sections.timeline')
        @endif

        @if (in_array('about', $sections, true))
            @include('portfolio.sections.about')
        @endif

        @include('portfolio.sections.contact')
    </main>

    @include('portfolio.sections.project-dialog')
</x-layouts.site>
