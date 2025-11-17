{{-- Button Block --}}
@php
    $text = $block->content['text'] ?? 'Button';
    $link = $block->content['link'] ?? '#';
    $openInNewTab = $block->content['openInNewTab'] ?? false;
    $variant = $block->properties['variant'] ?? 'primary';
    $size = $block->properties['size'] ?? 'medium';
    $align = $block->properties['align'] ?? 'left';
    $fullWidth = $block->properties['fullWidth'] ?? false;

    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700',
        'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
        'ghost' => 'text-blue-600 hover:bg-blue-50',
    ];

    $sizeClasses = [
        'small' => 'px-3 py-1.5 text-sm',
        'medium' => 'px-4 py-2 text-base',
        'large' => 'px-6 py-3 text-lg',
    ];
@endphp

<div class="py-4 text-{{ $align }}">
    <a
        href="{{ $link }}"
        @if($openInNewTab) target="_blank" rel="noopener noreferrer" @endif
        class="inline-flex items-center justify-center font-medium rounded-lg transition-colors
            {{ $variantClasses[$variant] ?? $variantClasses['primary'] }}
            {{ $sizeClasses[$size] ?? $sizeClasses['medium'] }}
            @if($fullWidth) w-full @endif"
    >
        {{ $text }}
    </a>
</div>
