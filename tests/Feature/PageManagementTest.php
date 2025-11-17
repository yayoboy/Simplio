<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can list pages for a site
     */
    public function test_authenticated_user_can_list_pages(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        Page::factory()->count(3)->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/sites/{$site->id}/pages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'slug', 'site_id', 'is_published', 'is_home'],
            ])
            ->assertJsonCount(3);
    }

    /**
     * Test authenticated user can create a page
     */
    public function test_authenticated_user_can_create_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        $pageData = [
            'title' => 'About Us',
            'slug' => 'about-us',
            'meta_title' => 'About Us - Company',
            'meta_description' => 'Learn about our company',
            'is_home' => false,
        ];

        $response = $this->postJson("/api/sites/{$site->id}/pages", $pageData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Page created successfully',
                'data' => [
                    'title' => 'About Us',
                    'slug' => 'about-us',
                    'site_id' => $site->id,
                    'is_published' => false,
                    'is_home' => false,
                ],
            ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'About Us',
            'slug' => 'about-us',
            'site_id' => $site->id,
        ]);
    }

    /**
     * Test page creation generates slug from title if not provided
     */
    public function test_page_creation_generates_slug_from_title(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/sites/{$site->id}/pages", [
            'title' => 'Contact Us Page',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.slug', 'contact-us-page');
    }

    /**
     * Test page creation requires valid data
     */
    public function test_page_creation_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        // Test missing title
        $response = $this->postJson("/api/sites/{$site->id}/pages", []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test page slug must be unique within site
     */
    public function test_page_slug_must_be_unique_within_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        Page::factory()->create([
            'site_id' => $site->id,
            'slug' => 'existing-page',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/sites/{$site->id}/pages", [
            'title' => 'Another Page',
            'slug' => 'existing-page',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test authenticated user can view a page
     */
    public function test_authenticated_user_can_view_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/pages/{$page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                ],
            ]);
    }

    /**
     * Test authenticated user can update a page
     */
    public function test_authenticated_user_can_update_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $updatedData = [
            'title' => 'Updated Page Title',
            'meta_description' => 'Updated description',
        ];

        $response = $this->putJson("/api/pages/{$page->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page updated successfully',
                'data' => [
                    'id' => $page->id,
                    'title' => 'Updated Page Title',
                ],
            ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Page Title',
        ]);
    }

    /**
     * Test authenticated user can delete a page
     */
    public function test_authenticated_user_can_delete_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/pages/{$page->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Page deleted successfully']);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    /**
     * Test authenticated user can publish a page
     */
    public function test_authenticated_user_can_publish_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/pages/{$page->id}/publish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page published successfully',
                'data' => [
                    'id' => $page->id,
                    'is_published' => true,
                ],
            ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'is_published' => true,
        ]);
    }

    /**
     * Test authenticated user can unpublish a page
     */
    public function test_authenticated_user_can_unpublish_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/pages/{$page->id}/unpublish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page unpublished successfully',
                'data' => [
                    'id' => $page->id,
                    'is_published' => false,
                ],
            ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'is_published' => false,
        ]);
    }

    /**
     * Test authenticated user can set page as home
     */
    public function test_authenticated_user_can_set_page_as_home(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        // Create existing home page
        $oldHome = Page::factory()->create([
            'site_id' => $site->id,
            'is_home' => true,
        ]);

        // Create new page to set as home
        $newHome = Page::factory()->create([
            'site_id' => $site->id,
            'is_home' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/pages/{$newHome->id}/set-home");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page set as home successfully',
                'data' => [
                    'id' => $newHome->id,
                    'is_home' => true,
                ],
            ]);

        // New home should be set
        $this->assertDatabaseHas('pages', [
            'id' => $newHome->id,
            'is_home' => true,
        ]);

        // Old home should be unset
        $this->assertDatabaseHas('pages', [
            'id' => $oldHome->id,
            'is_home' => false,
        ]);
    }

    /**
     * Test authenticated user can duplicate a page
     */
    public function test_authenticated_user_can_duplicate_page(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $site->id,
            'title' => 'Original Page',
            'is_published' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/pages/{$page->id}/duplicate", [
            'title' => 'Duplicated Page',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Page duplicated successfully',
                'data' => [
                    'title' => 'Duplicated Page',
                    'site_id' => $site->id,
                    'is_published' => false, // Duplicates should start unpublished
                    'is_home' => false,
                ],
            ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'Duplicated Page',
            'site_id' => $site->id,
        ]);

        // Original should still exist
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Original Page',
        ]);
    }

    /**
     * Test only one page can be home per site
     */
    public function test_only_one_page_can_be_home_per_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        $page1 = Page::factory()->create([
            'site_id' => $site->id,
            'is_home' => false,
        ]);

        $page2 = Page::factory()->create([
            'site_id' => $site->id,
            'is_home' => false,
        ]);

        Sanctum::actingAs($user);

        // Set first page as home
        $this->postJson("/api/pages/{$page1->id}/set-home");

        // Set second page as home
        $this->postJson("/api/pages/{$page2->id}/set-home");

        // Only page2 should be home
        $this->assertDatabaseHas('pages', [
            'id' => $page1->id,
            'is_home' => false,
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page2->id,
            'is_home' => true,
        ]);

        // Count home pages for this site
        $homeCount = Page::where('site_id', $site->id)
            ->where('is_home', true)
            ->count();

        $this->assertEquals(1, $homeCount);
    }

    /**
     * Test unauthenticated users cannot access page management
     */
    public function test_unauthenticated_users_cannot_access_page_management(): void
    {
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        $response = $this->getJson("/api/sites/{$site->id}/pages");
        $response->assertStatus(401);

        $response = $this->postJson("/api/sites/{$site->id}/pages", []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/pages/{$page->id}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/pages/{$page->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/pages/{$page->id}");
        $response->assertStatus(401);
    }
}
