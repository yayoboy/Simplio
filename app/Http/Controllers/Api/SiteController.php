<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Models\Site;
use App\Services\SiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function __construct(
        protected SiteService $siteService
    ) {
        $this->authorizeResource(Site::class, 'site');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $sites = $request->user()
            ->sites()
            ->with('theme')
            ->withCount('pages')
            ->latest()
            ->paginate(15);

        return response()->json($sites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSiteRequest $request): JsonResponse
    {
        $site = $this->siteService->createSite(
            $request->user(),
            $request->validated()
        );

        return response()->json($site->load('theme'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site): JsonResponse
    {
        return response()->json(
            $site->load(['theme', 'pages.blocks'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiteRequest $request, Site $site): JsonResponse
    {
        $site = $this->siteService->updateSite(
            $site,
            $request->validated()
        );

        return response()->json($site->load('theme'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site): JsonResponse
    {
        $this->siteService->deleteSite($site);

        return response()->json(null, 204);
    }

    /**
     * Publish the site.
     */
    public function publish(Site $site): JsonResponse
    {
        $this->authorize('update', $site);
        $this->siteService->publishSite($site);

        return response()->json($site->fresh());
    }

    /**
     * Unpublish the site.
     */
    public function unpublish(Site $site): JsonResponse
    {
        $this->authorize('update', $site);
        $this->siteService->unpublishSite($site);

        return response()->json($site->fresh());
    }

    /**
     * Duplicate the site.
     */
    public function duplicate(Request $request, Site $site): JsonResponse
    {
        $this->authorize('view', $site);

        $newSite = $this->siteService->duplicateSite(
            $site,
            $request->input('name', $site->name . ' (Copy)')
        );

        return response()->json($newSite, 201);
    }
}
