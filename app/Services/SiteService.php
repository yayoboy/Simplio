<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SiteService
{
    public function createSite(User $user, array $data): Site
    {
        return DB::transaction(function () use ($user, $data) {
            $site = $user->sites()->create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? null,
                'description' => $data['description'] ?? null,
                'theme_id' => $data['theme_id'] ?? null,
                'settings' => $data['settings'] ?? [],
                'meta' => $data['meta'] ?? [],
            ]);

            return $site;
        });
    }

    public function updateSite(Site $site, array $data): Site
    {
        $site->update($data);
        return $site->fresh();
    }

    public function deleteSite(Site $site): bool
    {
        return $site->delete();
    }

    public function publishSite(Site $site): bool
    {
        return $site->publish();
    }

    public function unpublishSite(Site $site): bool
    {
        return $site->unpublish();
    }

    public function duplicateSite(Site $site, string $newName): Site
    {
        return DB::transaction(function () use ($site, $newName) {
            $newSite = $site->replicate();
            $newSite->name = $newName;
            $newSite->slug = null;
            $newSite->is_published = false;
            $newSite->save();

            // Duplicate pages
            foreach ($site->pages as $page) {
                $newPage = $page->replicate();
                $newPage->site_id = $newSite->id;
                $newPage->is_published = false;
                $newPage->save();

                // Duplicate blocks
                foreach ($page->blocks as $block) {
                    $newBlock = $block->replicate();
                    $newBlock->page_id = $newPage->id;
                    $newBlock->save();
                }
            }

            return $newSite;
        });
    }
}
