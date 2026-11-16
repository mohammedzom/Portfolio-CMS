@php
    $projects = $page['projects'];
    $categories = $page['categories'];
@endphp

<section class="section" id="work" aria-labelledby="work-title" x-data="projectGrid">
    <div class="wrap">
        <x-site.section-head path="/work" title-id="work-title">
            <x-slot:title>Selected projects</x-slot:title>
            <x-slot:subtitle>APIs, bots and infrastructure I have built and deployed. Open any project for the details.</x-slot:subtitle>
        </x-site.section-head>

        @if ($projects === [])
            <x-site.empty-state path="/work">No projects have been published yet.</x-site.empty-state>
        @else
            @if (count($categories) > 1)
                <div class="filters" role="toolbar" aria-label="Filter projects by category">
                    <button type="button" class="chip is-active" aria-pressed="true" data-filter="all" @click="apply('all')">All <span class="chip__n">{{ count($projects) }}</span></button>
                    @foreach ($categories as $category)
                        <button type="button" class="chip" aria-pressed="false" data-filter="{{ $category['key'] }}" @click="apply(@js($category['key']))">{{ $category['label'] }} <span class="chip__n">{{ $category['count'] }}</span></button>
                    @endforeach
                </div>
                <p class="sr-only" aria-live="polite" x-text="status"></p>
            @endif

            <div class="bento" x-ref="bento">
                @foreach ($projects as $project)
                    <x-site.project-card :project="$project" :index="$loop->index" />
                @endforeach
            </div>
        @endif
    </div>
</section>
