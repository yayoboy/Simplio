<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $themes = Theme::active()->latest()->get();

        return response()->json($themes);
    }

    /**
     * Get global themes.
     */
    public function global(): JsonResponse
    {
        $themes = Theme::global()->active()->get();

        return response()->json($themes);
    }

    /**
     * Display the specified resource.
     */
    public function show(Theme $theme): JsonResponse
    {
        return response()->json($theme);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:themes,slug',
            'description' => 'nullable|string',
            'design_tokens' => 'required|array',
            'is_global' => 'sometimes|boolean',
        ]);

        $theme = Theme::create(array_merge($validated, [
            'created_by' => $request->user()->id,
        ]));

        return response()->json($theme, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Theme $theme): JsonResponse
    {
        $this->authorize('update', $theme);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'design_tokens' => 'sometimes|array',
        ]);

        $theme->update($validated);

        return response()->json($theme->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme): JsonResponse
    {
        $this->authorize('delete', $theme);

        $theme->delete();

        return response()->json(null, 204);
    }
}
