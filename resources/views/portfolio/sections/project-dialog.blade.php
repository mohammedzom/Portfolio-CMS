@php
    $projects = $page['projects'];
    $dialogData = collect($projects)->map(fn (array $project): array => [
        'slug' => $project['slug'],
        'title' => $project['title'],
        'category' => $project['category'],
        'description' => $project['description'],
        'points' => $project['points'],
        'tags' => $project['tags'],
        'images' => $project['images'],
        'liveUrl' => $project['liveUrl'],
        'repoUrl' => $project['repoUrl'],
    ])->values()->all();
@endphp

@if ($projects !== [])
    {{-- Public project data for the dialog. It only contains what the page already shows. --}}
    <script type="application/json" id="project-data">{!! json_encode($dialogData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    <dialog
        class="modal"
        id="project-modal"
        aria-labelledby="modal-title"
        x-data="projectDialog"
        @open-project.window="show($event.detail.slug, $event.detail.trigger)"
        @cancel.prevent="close()"
        @close="restoreFocus()"
        @click="if ($event.target === $el) close()"
        @keydown="onKeydown($event)"
    >
        <div class="modal__card">
            <div class="modal__media">
                @foreach ($projects as $project)
                    <template x-if="project && project.slug === @js($project['slug']) && (! image || imageFailed)">
                        <x-site.pattern :type="$project['pattern']" :label="'Abstract illustration for '.$project['title']" />
                    </template>
                @endforeach

                <template x-if="image && ! imageFailed">
                    <div class="modal__shot">
                        <img class="modal__shot-backdrop" :src="image" alt="" aria-hidden="true" decoding="async">
                        <img class="modal__shot-image" :src="image" :alt="'Screenshot ' + (imageIndex + 1) + ' of ' + project.title" decoding="async" x-on:error="imageFailed = true">
                    </div>
                </template>

                <div class="gallery" x-show="project && project.images.length > 1" x-cloak>
                    <button type="button" class="gallery__btn" aria-label="Previous screenshot" @click="stepImage(-1)">
                        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M15 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <span class="gallery__count" aria-live="polite" x-text="project ? (imageIndex + 1) + ' / ' + project.images.length : ''"></span>
                    <button type="button" class="gallery__btn" aria-label="Next screenshot" @click="stepImage(1)">
                        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <div class="modal__body" x-ref="body">
                <div class="modal__top">
                    <p class="modal__cat" x-text="project?.category"></p>
                    <button type="button" class="modal__close" x-ref="close" aria-label="Close project details" @click="close()">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <h3 class="modal__title" id="modal-title" x-text="project?.title"></h3>

                <div class="modal__desc">
                    <template x-for="(paragraph, index) in project?.description ?? []" :key="index">
                        <p x-text="paragraph"></p>
                    </template>
                </div>

                <template x-if="project && project.points.length">
                    <div>
                        <h4 class="modal__h">What it involves</h4>
                        <ul class="modal__points">
                            <template x-for="(point, index) in project.points" :key="index">
                                <li><template x-if="point.label"><strong x-text="point.label + '. '"></strong></template><span x-text="point.text"></span></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <template x-if="project && project.tags.length">
                    <div>
                        <h4 class="modal__h">Built with</h4>
                        <ul class="tags">
                            <template x-for="tag in project.tags" :key="tag">
                                <li class="tag" x-text="tag"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <div class="modal__links">
                    <template x-if="project?.liveUrl">
                        <a class="btn btn--primary btn--small" :href="project.liveUrl" target="_blank" rel="noopener">Visit live project <x-site.external-icon /></a>
                    </template>
                    <template x-if="project?.repoUrl">
                        <a class="btn btn--small" :class="project.liveUrl ? 'btn--ghost' : 'btn--primary'" :href="project.repoUrl" target="_blank" rel="noopener">View repository <x-site.external-icon /></a>
                    </template>
                    <template x-if="project && ! project.repoUrl">
                        <span class="modal__note">This project has no public repository.</span>
                    </template>
                    <template x-if="project && ! project.repoUrl && ! project.liveUrl">
                        <a class="btn btn--ghost btn--small" href="#contact" @click="close()">Ask me about it</a>
                    </template>
                </div>

                <div class="modal__nav" x-show="projects.length > 1">
                    <button type="button" class="btn btn--text btn--small" @click="step(-1)">Previous project</button>
                    <span class="modal__count" x-text="(index + 1) + ' / ' + projects.length"></span>
                    <button type="button" class="btn btn--text btn--small" @click="step(1)">Next project</button>
                </div>
            </div>
        </div>
    </dialog>
@endif
