{{-- Columns Block --}}
@php
    $columns = $block->content['columns'] ?? [];
    $gap = $block->properties['gap'] ?? '1rem';
    $verticalAlign = $block->properties['verticalAlign'] ?? 'top';

    $alignMap = [
        'top' => 'start',
        'center' => 'center',
        'bottom' => 'end',
    ];
@endphp

@if(count($columns) > 0)
    <div class="py-4">
        <div class="grid grid-flow-col auto-cols-fr" style="gap: {{ $gap }}; align-items: {{ $alignMap[$verticalAlign] ?? 'start' }};">
            @foreach($columns as $column)
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 min-h-[100px]">
                    <p class="text-gray-500 text-sm text-center">Column (nested blocks coming soon)</p>
                </div>
            @endforeach
        </div>
    </div>
@endif
