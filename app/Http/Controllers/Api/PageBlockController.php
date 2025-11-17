<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageBlockRequest;
use App\Http\Requests\UpdatePageBlockRequest;
use App\Models\Page;
use App\Models\PageBlock;
use App\Services\BlockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageBlockController extends Controller
{
    public function __construct(
        protected BlockService $blockService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Page $page): JsonResponse
    {
        $this->authorize('view', $page);

        $blocks = $page->blocks()->ordered()->get();

        return response()->json($blocks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageBlockRequest $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $block = $this->blockService->createBlock(
            $page,
            $request->validated()
        );

        return response()->json($block, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page, PageBlock $block): JsonResponse
    {
        $this->authorize('view', $page);

        return response()->json($block);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageBlockRequest $request, Page $page, PageBlock $block): JsonResponse
    {
        $this->authorize('update', $page);

        $block = $this->blockService->updateBlock(
            $block,
            $request->validated()
        );

        return response()->json($block);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page, PageBlock $block): JsonResponse
    {
        $this->authorize('update', $page);

        $this->blockService->deleteBlock($block);

        return response()->json(null, 204);
    }

    /**
     * Reorder blocks.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'blocks' => 'required|array',
            'blocks.*' => 'exists:page_blocks,id',
        ]);

        $page = Page::findOrFail($request->input('page_id'));
        $this->authorize('update', $page);

        $this->blockService->reorderBlocks($page, $request->input('blocks'));

        return response()->json(['message' => 'Blocks reordered successfully']);
    }
}
