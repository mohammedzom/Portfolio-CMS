@props(['path'])

{{-- Shown instead of a section's content when the CMS has nothing to list yet. --}}
<div {{ $attributes->class('empty-state') }} data-reveal>
    <span class="empty-state__route" aria-hidden="true">GET {{ $path }} · 204 No Content</span>
    {{ $slot }}
</div>
