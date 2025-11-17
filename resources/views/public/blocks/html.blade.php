{{-- HTML Block --}}
@php
    $html = $block->content['html'] ?? '';
    $sanitize = $block->properties['sanitize'] ?? true;
@endphp

@if($html && !$sanitize)
    <div class="py-4">
        <div class="custom-html">
            {!! $html !!}
        </div>
    </div>
@elseif($html && $sanitize)
    {{-- Don't render HTML if sanitization is enabled for security --}}
    <div class="py-4">
        <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-4 text-sm text-yellow-700">
            ⚠️ HTML content is sanitized and cannot be displayed for security reasons.
        </div>
    </div>
@endif
