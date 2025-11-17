{{-- Container Block --}}
@php
    $maxWidth = $block->properties['maxWidth'] ?? '1200px';
    $padding = $block->properties['padding'] ?? '1rem';
    $backgroundColor = $block->properties['backgroundColor'] ?? 'transparent';
@endphp

<div class="py-4">
    <div style="max-width: {{ $maxWidth }}; padding: {{ $padding }}; background-color: {{ $backgroundColor }}; margin: 0 auto;" class="rounded-lg">
        <p class="text-gray-500 text-sm text-center">Container Block (nested blocks coming soon)</p>
    </div>
</div>
