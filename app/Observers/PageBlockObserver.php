<?php

namespace App\Observers;

use App\Models\PageBlock;
use App\Services\PageCacheService;

class PageBlockObserver
{
    public function __construct(
        protected PageCacheService $cacheService
    ) {
    }

    /**
     * Handle the PageBlock "created" event.
     */
    public function created(PageBlock $pageBlock): void
    {
        // Invalidate cache when a block is added
        $this->cacheService->invalidatePageOnBlockUpdate($pageBlock->page_id);
    }

    /**
     * Handle the PageBlock "updated" event.
     */
    public function updated(PageBlock $pageBlock): void
    {
        // Invalidate cache when a block is modified
        $this->cacheService->invalidatePageOnBlockUpdate($pageBlock->page_id);
    }

    /**
     * Handle the PageBlock "deleted" event.
     */
    public function deleted(PageBlock $pageBlock): void
    {
        // Invalidate cache when a block is deleted
        $this->cacheService->invalidatePageOnBlockUpdate($pageBlock->page_id);
    }

    /**
     * Handle the PageBlock "restored" event.
     */
    public function restored(PageBlock $pageBlock): void
    {
        // Invalidate cache when a block is restored
        $this->cacheService->invalidatePageOnBlockUpdate($pageBlock->page_id);
    }

    /**
     * Handle the PageBlock "force deleted" event.
     */
    public function forceDeleted(PageBlock $pageBlock): void
    {
        // Invalidate cache when a block is permanently deleted
        $this->cacheService->invalidatePageOnBlockUpdate($pageBlock->page_id);
    }
}
