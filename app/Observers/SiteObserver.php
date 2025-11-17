<?php

namespace App\Observers;

use App\Models\Site;
use App\Services\PageCacheService;

class SiteObserver
{
    public function __construct(
        protected PageCacheService $cacheService
    ) {
    }

    /**
     * Handle the Site "updated" event.
     */
    public function updated(Site $site): void
    {
        // When site is updated (including theme changes), invalidate all pages
        $this->cacheService->invalidateSiteOnUpdate($site);
    }

    /**
     * Handle the Site "deleted" event.
     */
    public function deleted(Site $site): void
    {
        // Clear all cache for this site
        $this->cacheService->invalidateSite($site);
    }
}
