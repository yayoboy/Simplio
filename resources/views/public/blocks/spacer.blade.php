{{-- Spacer Block --}}
@php
    $height = $block->properties['height'] ?? '2rem';
@endphp

<div style="height: {{ $height }}; min-height: 1rem;"></div>
