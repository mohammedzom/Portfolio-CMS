@props(['verb' => 'GET', 'path', 'status' => 200])

{{-- The "portfolio as an API" label shown above each section heading. --}}
<p {{ $attributes->class('route') }} aria-hidden="true"><span class="route__verb">{{ $verb }}</span> {{ $path }} <span class="route__status">{{ $status }}</span></p>
