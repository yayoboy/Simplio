<?php

namespace App\Observers;

use App\Models\Page;
use App\Services\PageCacheService;

class PageObserver
{
    public function __construct(
        protected PageCacheService $cacheService
    ) {
    }

    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        // Invalidate cache for the site
        if ($page->site) {
            $this->cacheService->invalidateSite($page->site);
        }
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        // Invalidate cache for this page and potentially site home
        $this->cacheService->invalidatePageOnUpdate($page);

        // If home status changed, invalidate entire site
        if ($page->isDirty('is_home') || $page->isDirty('is_published')) {
            if ($page->site) {
                $this->cacheService->invalidateSite($page->site);
            }
        }
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        // Invalidate cache for the entire site when a page is deleted
        if ($page->site) {
            $this->cacheService->invalidateSite($page->site);
        }
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        // Invalidate cache when page is restored
        if ($page->site) {
            $this->cacheService->invalidateSite($page->site);
        }
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        // Invalidate cache for the site
        if ($page->site) {
            $this->cacheService->invalidateSite($page->site);
        }
    }
}
