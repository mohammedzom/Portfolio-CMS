@props([
    'id',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-dark-950/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('{{ $id }}')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-dark-900 rounded-2xl text-left overflow-hidden shadow-neon-lg transform transition-all sm:my-8 sm:align-middle {{ $sizeClass }} w-full border border-dark-800">
            @if($title)
                <div class="px-6 py-4 border-b border-dark-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-dark-100 font-display" id="modal-title">
                        {{ $title }}
                    </h3>
                    <button type="button" class="text-dark-500 hover:text-neon-500 transition-colors" onclick="closeModal('{{ $id }}')">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
            @endif

            <div class="px-6 py-6">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="px-6 py-4 border-t border-dark-800 flex items-center justify-end gap-3 bg-dark-950/30">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('[role="dialog"]:not(.hidden)');
            modals.forEach(modal => closeModal(modal.id));
        }
    });
</script>
@endpush
@endonce
