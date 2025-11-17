<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Show site home page
     */
    public function showSite(string $siteSlug): View
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

        return $this->renderPage($site, $page);
    }

    /**
     * Show specific page
     */
    public function showPage(string $siteSlug, string $pageSlug): View
    {
        $site = Site::where('slug', $siteSlug)
            ->where('is_published', true)
            ->with('theme')
            ->firstOrFail();

        $page = Page::where('site_id', $site->id)
            ->where('slug', $pageSlug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->renderPage($site, $page);
    }

    /**
     * Render page with theme and blocks
     */
    protected function renderPage(Site $site, Page $page): View
    {
        // Load blocks ordered by position
        $blocks = $page->blocks()
            ->where('is_visible', true)
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
        ]);
    }
}
