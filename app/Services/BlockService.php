<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Support\Facades\DB;

class BlockService
{
    public function createBlock(Page $page, array $data): PageBlock
    {
        return $page->blocks()->create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
            'content' => $data['content'] ?? [],
            'properties' => $data['properties'] ?? [],
            'position' => $data['position'] ?? [],
            'order' => $data['order'] ?? $page->blocks()->count(),
            'is_visible' => $data['is_visible'] ?? true,
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    public function updateBlock(PageBlock $block, array $data): PageBlock
    {
        $block->update($data);
        return $block->fresh();
    }

    public function deleteBlock(PageBlock $block): bool
    {
        return $block->delete();
    }

    public function duplicateBlock(PageBlock $block): PageBlock
    {
        return $block->duplicate();
    }

    public function reorderBlocks(Page $page, array $blockIds): void
    {
        DB::transaction(function () use ($blockIds) {
            foreach ($blockIds as $index => $blockId) {
                PageBlock::where('id', $blockId)->update(['order' => $index]);
            }
        });
    }

    public function updateBlockContent(PageBlock $block, array $content): PageBlock
    {
        $block->updateContent($content);
        return $block->fresh();
    }

    public function updateBlockPosition(PageBlock $block, array $position): PageBlock
    {
        $block->updatePosition($position);
        return $block->fresh();
    }
}
