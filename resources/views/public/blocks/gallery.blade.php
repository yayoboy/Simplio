{{-- Gallery Block --}}
@php
    $images = $block->content['images'] ?? [];
    $columns = $block->properties['columns'] ?? 3;
    $gap = $block->properties['gap'] ?? '1rem';
    $aspectRatio = $block->properties['aspectRatio'] ?? '16/9';
@endphp

@if(count($images) > 0)
    <div class="py-4">
        <div class="grid grid-cols-{{ $columns }}" style="gap: {{ $gap }};">
            @foreach($images as $image)
                <div class="relative overflow-hidden rounded-lg">
                    <img
                        src="{{ is_array($image) ? ($image['src'] ?? '') : $image }}"
                        alt="{{ is_array($image) ? ($image['alt'] ?? '') : '' }}"
                        class="w-full h-full object-cover"
                        style="aspect-ratio: {{ $aspectRatio }};"
                        loading="lazy"
                    />
                    @if(is_array($image) && isset($image['caption']))
                        <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 text-white text-sm p-2">
                            {{ $image['caption'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
