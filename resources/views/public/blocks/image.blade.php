{{-- Image Block --}}
@php
    $src = $block->content['src'] ?? '';
    $alt = $block->content['alt'] ?? '';
    $caption = $block->content['caption'] ?? '';
    $align = $block->properties['align'] ?? 'center';
    $width = $block->properties['width'] ?? '100%';
@endphp

<div class="py-4 text-{{ $align }}">
    @if($src)
        <div class="inline-block" style="width: {{ $width }};">
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                class="w-full rounded-lg object-cover"
                loading="lazy"
            />
            @if($caption)
                <p class="mt-2 text-sm text-gray-600 text-center">{{ $caption }}</p>
            @endif
        </div>
    @endif
</div>
