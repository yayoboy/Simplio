{{-- Heading Block --}}
@php
    $level = $block->content['level'] ?? 2;
    $text = $block->content['text'] ?? 'Heading';
    $align = $block->properties['textAlign'] ?? 'left';
    $tag = 'h' . $level;
@endphp

<{{ $tag }} class="font-bold text-{{ $align }} py-4
    @if($level == 1) text-4xl md:text-5xl
    @elseif($level == 2) text-3xl md:text-4xl
    @elseif($level == 3) text-2xl md:text-3xl
    @elseif($level == 4) text-xl md:text-2xl
    @elseif($level == 5) text-lg md:text-xl
    @else text-base md:text-lg
    @endif">
    {{ $text }}
</{{ $tag }}>
