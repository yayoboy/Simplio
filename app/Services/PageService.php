<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Site;
use Illuminate\Support\Facades\DB;

class PageService
{
    public function createPage(Site $site, array $data): Page
    {
        return DB::transaction(function () use ($site, $data) {
            $page = $site->pages()->create([
                'title' => $data['title'],
                'slug' => $data['slug'] ?? null,
                'description' => $data['description'] ?? null,
                'layout' => $data['layout'] ?? [],
                'settings' => $data['settings'] ?? [],
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta' => $data['meta'] ?? [],
                'is_home' => $data['is_home'] ?? false,
                'template' => $data['template'] ?? null,
                'order' => $data['order'] ?? 0,
            ]);

            return $page;
        });
    }

    public function updatePage(Page $page, array $data): Page
    {
        $page->update($data);
        return $page->fresh();
    }

    public function deletePage(Page $page): bool
    {
        return $page->delete();
    }

    public function publishPage(Page $page): bool
    {
        return $page->publish();
    }

    public function unpublishPage(Page $page): bool
    {
        return $page->unpublish();
    }

    public function duplicatePage(Page $page): Page
    {
        return $page->duplicate();
    }

    public function reorderPages(Site $site, array $pageIds): void
    {
        DB::transaction(function () use ($pageIds) {
            foreach ($pageIds as $index => $pageId) {
                Page::where('id', $pageId)->update(['order' => $index]);
            }
        });
    }
}
