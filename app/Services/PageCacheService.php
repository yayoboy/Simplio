<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Site;
use Illuminate\Support\Facades\Cache;

class PageCacheService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected int $cacheDuration = 3600;

    /**
     * Get cache key for a site's home page
     */
    public function getSiteHomeCacheKey(Site $site): string
    {
        return "site.{$site->slug}.home";
    }

    /**
     * Get cache key for a specific page
     */
    public function getPageCacheKey(Site $site, Page $page): string
    {
        return "site.{$site->slug}.page.{$page->slug}";
    }

    /**
     * Get cache key for all pages of a site
     */
    public function getSiteCachePattern(Site $site): string
    {
        return "site.{$site->slug}.*";
    }

    /**
     * Cache rendered page HTML
     */
    public function cachePage(string $key, string $html): void
    {
        Cache::put($key, $html, $this->cacheDuration);
    }

    /**
     * Get cached page HTML
     */
    public function getCachedPage(string $key): ?string
    {
        return Cache::get($key);
    }

    /**
     * Check if page is cached
     */
    public function isCached(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Invalidate (clear) cache for a specific page
     */
    public function invalidatePage(Site $site, Page $page): void
    {
        $key = $this->getPageCacheKey($site, $page);
        Cache::forget($key);

        // If this is the home page, also clear the home cache
        if ($page->is_home) {
            Cache::forget($this->getSiteHomeCacheKey($site));
        }
    }

    /**
     * Invalidate all cached pages for a site
     */
    public function invalidateSite(Site $site): void
    {
        // Clear home page
        Cache::forget($this->getSiteHomeCacheKey($site));

        // Clear all pages for this site
        // Note: This is a simple implementation. For production with Redis,
        // you might want to use tags or scan for keys with pattern
        foreach ($site->pages as $page) {
            Cache::forget($this->getPageCacheKey($site, $page));
        }
    }

    /**
     * Invalidate cache when site is updated
     */
    public function invalidateSiteOnUpdate(Site $site): void
    {
        // When site (or theme) is updated, invalidate all pages
        $this->invalidateSite($site);
    }

    /**
     * Invalidate cache when page content is updated
     */
    public function invalidatePageOnUpdate(Page $page): void
    {
        if ($page->site) {
            $this->invalidatePage($page->site, $page);
        }
    }

    /**
     * Invalidate cache when a block is updated
     */
    public function invalidatePageOnBlockUpdate(int $pageId): void
    {
        $page = Page::with('site')->find($pageId);

        if ($page && $page->site) {
            $this->invalidatePage($page->site, $page);
        }
    }

    /**
     * Warm up cache by pre-rendering a page
     */
    public function warmUp(Site $site, Page $page, callable $renderCallback): string
    {
        $key = $page->is_home
            ? $this->getSiteHomeCacheKey($site)
            : $this->getPageCacheKey($site, $page);

        // Render and cache
        $html = $renderCallback();
        $this->cachePage($key, $html);

        return $html;
    }

    /**
     * Clear all page cache
     */
    public function clearAll(): void
    {
        // This is a simple implementation
        // For production, you might want to use cache tags
        Cache::flush();
    }

    /**
     * Set cache duration
     */
    public function setCacheDuration(int $seconds): self
    {
        $this->cacheDuration = $seconds;
        return $this;
    }

    /**
     * Get cache statistics (if supported by driver)
     */
    public function getStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'duration' => $this->cacheDuration,
        ];
    }
}
