{{-- Text Block --}}
<div class="prose @if($block->properties['textAlign'] ?? 'left') text-{{ $block->properties['textAlign'] }} @endif @if($block->properties['fontSize'] ?? 'base') text-{{ $block->properties['fontSize'] }} @endif py-4">
    {!! $block->content['text'] ?? '<p>No content</p>' !!}
</div>
