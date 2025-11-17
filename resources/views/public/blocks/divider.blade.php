{{-- Divider Block --}}
@php
    $style = $block->properties['style'] ?? 'solid';
    $thickness = $block->properties['thickness'] ?? '1px';
    $color = $block->properties['color'] ?? '#e5e7eb';
    $spacing = $block->properties['spacing'] ?? '2rem';
@endphp

<div style="padding-top: {{ $spacing }}; padding-bottom: {{ $spacing }};">
    @if($style === 'solid')
        <hr style="height: {{ $thickness }}; background-color: {{ $color }}; border: none;">
    @else
        <hr style="border: 0; border-top: {{ $thickness }} {{ $style }} {{ $color }};">
    @endif
</div>
