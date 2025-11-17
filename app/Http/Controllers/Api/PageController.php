<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Models\Site;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(
        protected PageService $pageService
    ) {
        $this->authorizeResource(Page::class, 'page');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Site $site): JsonResponse
    {
        $this->authorize('view', $site);

        $pages = $site->pages()
            ->withCount('blocks')
            ->ordered()
            ->get();

        return response()->json($pages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request, Site $site): JsonResponse
    {
        $this->authorize('update', $site);

        $page = $this->pageService->createPage(
            $site,
            $request->validated()
        );

        return response()->json($page->load('blocks'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site, Page $page): JsonResponse
    {
        return response()->json(
            $page->load(['blocks' => function ($query) {
                $query->ordered();
            }, 'site.theme'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Site $site, Page $page): JsonResponse
    {
        $page = $this->pageService->updatePage(
            $page,
            $request->validated()
        );

        return response()->json($page->load('blocks'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site, Page $page): JsonResponse
    {
        $this->pageService->deletePage($page);

        return response()->json(null, 204);
    }

    /**
     * Publish the page.
     */
    public function publish(Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $this->pageService->publishPage($page);

        return response()->json($page->fresh());
    }

    /**
     * Unpublish the page.
     */
    public function unpublish(Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $this->pageService->unpublishPage($page);

        return response()->json($page->fresh());
    }

    /**
     * Duplicate the page.
     */
    public function duplicate(Page $page): JsonResponse
    {
        $this->authorize('view', $page);

        $newPage = $this->pageService->duplicatePage($page);

        return response()->json($newPage->load('blocks'), 201);
    }

    /**
     * Set page as home page.
     */
    public function setAsHome(Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $page->setAsHome();

        return response()->json($page->fresh());
    }
}
