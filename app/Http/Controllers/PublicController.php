<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Page;
use App\Services\PageCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PublicController extends Controller
{
    public function __construct(
        protected PageCacheService $cacheService
    ) {
    }

    /**
     * Show site home page
     */
    public function showSite(string $siteSlug): Response
    {
        $site = Site::where('slug', $siteSlug)
            ->where('is_published', true)
            ->with(['theme', 'pages' => function ($query) {
                $query->where('is_published', true)
                    ->where('is_home', true);
            }])
            ->firstOrFail();

        $page = $site->pages->first();

        if (!$page) {
            abort(404, 'No home page found for this site');
        }

        return $this->renderPageWithCache($site, $page, true);
    }

    /**
     * Show specific page
     */
    public function showPage(string $siteSlug, string $pageSlug): Response
    {
        $site = Site::where('slug', $siteSlug)
            ->where('is_published', true)
            ->with('theme')
            ->firstOrFail();

        $page = Page::where('site_id', $site->id)
            ->where('slug', $pageSlug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->renderPageWithCache($site, $page, false);
    }

    /**
     * Render page with caching
     */
    protected function renderPageWithCache(Site $site, Page $page, bool $isHome): Response
    {
        $cacheKey = $isHome
            ? $this->cacheService->getSiteHomeCacheKey($site)
            : $this->cacheService->getPageCacheKey($site, $page);

        // Try to get from cache
        $cachedHtml = $this->cacheService->getCachedPage($cacheKey);

        if ($cachedHtml !== null) {
            return response($cachedHtml)
                ->header('X-Cache', 'HIT')
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        // Cache miss - render the page
        $html = $this->renderPage($site, $page);

        // Cache the rendered HTML
        $this->cacheService->cachePage($cacheKey, $html);

        return response($html)
            ->header('X-Cache', 'MISS')
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Render page with theme and blocks
     */
    protected function renderPage(Site $site, Page $page): string
    {
        // Load blocks ordered by position (only root blocks with children)
        $blocks = $page->blocks()
            ->whereNull('parent_id')
            ->where('is_visible', true)
            ->with('children')
            ->ordered()
            ->get();

        // Generate theme CSS
        $themeCss = $site->theme ? $site->theme->generateCss() : '';

        return view('public.page', [
            'site' => $site,
            'page' => $page,
            'blocks' => $blocks,
            'theme' => $site->theme,
            'themeCss' => $themeCss,
        ])->render();
    }
}
