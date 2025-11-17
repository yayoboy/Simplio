<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Site;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicPageRenderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public can view published site home page
     */
    public function test_public_can_view_published_site_home_page(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Test public can view published page by slug
     */
    public function test_public_can_view_published_page_by_slug(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'slug' => 'about',
            'is_published' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}/{$page->slug}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Test unpublished site returns 404
     */
    public function test_unpublished_site_returns_404(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => false,
        ]);

        Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test unpublished page returns 404
     */
    public function test_unpublished_page_returns_404(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'slug' => 'about',
            'is_published' => false,
        ]);

        $response = $this->get("/sites/{$site->slug}/{$page->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test site without home page returns 404
     */
    public function test_site_without_home_page_returns_404(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        // No home page created

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test page caching works with cache miss
     */
    public function test_page_caching_works_with_cache_miss(): void
    {
        Cache::flush();

        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertHeader('X-Cache', 'MISS');
    }

    /**
     * Test page caching works with cache hit
     */
    public function test_page_caching_works_with_cache_hit(): void
    {
        Cache::flush();

        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        // First request - cache miss
        $response1 = $this->get("/sites/{$site->slug}");
        $response1->assertHeader('X-Cache', 'MISS');

        // Second request - cache hit
        $response2 = $this->get("/sites/{$site->slug}");
        $response2->assertStatus(200)
            ->assertHeader('X-Cache', 'HIT');
    }

    /**
     * Test page renders with blocks
     */
    public function test_page_renders_with_blocks(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        // Add some blocks
        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'heading',
            'content' => ['text' => 'Welcome', 'level' => 'h1'],
            'is_visible' => true,
            'position' => 0,
        ]);

        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'This is the home page content'],
            'is_visible' => true,
            'position' => 1,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertSee('Welcome')
            ->assertSee('This is the home page content');
    }

    /**
     * Test invisible blocks are not rendered
     */
    public function test_invisible_blocks_are_not_rendered(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        // Visible block
        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Visible content'],
            'is_visible' => true,
        ]);

        // Invisible block
        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Hidden content'],
            'is_visible' => false,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertSee('Visible content')
            ->assertDontSee('Hidden content');
    }

    /**
     * Test page renders with theme CSS
     */
    public function test_page_renders_with_theme_css(): void
    {
        $theme = Theme::factory()->create([
            'name' => 'Test Theme',
            'design_tokens' => [
                'colors' => [
                    'primary' => '#3b82f6',
                ],
            ],
        ]);

        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
            'theme_id' => $theme->id,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertSee('#3b82f6', false); // Theme color in CSS
    }

    /**
     * Test non-existent site returns 404
     */
    public function test_non_existent_site_returns_404(): void
    {
        $response = $this->get('/sites/non-existent-site');

        $response->assertStatus(404);
    }

    /**
     * Test non-existent page returns 404
     */
    public function test_non_existent_page_returns_404(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $response = $this->get("/sites/{$site->slug}/non-existent-page");

        $response->assertStatus(404);
    }

    /**
     * Test blocks are rendered in correct order
     */
    public function test_blocks_are_rendered_in_correct_order(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        // Create blocks in random order
        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Third'],
            'is_visible' => true,
            'position' => 2,
        ]);

        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'First'],
            'is_visible' => true,
            'position' => 0,
        ]);

        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Second'],
            'is_visible' => true,
            'position' => 1,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $html = $response->getContent();

        // Check that "First" appears before "Second" in the HTML
        $posFirst = strpos($html, 'First');
        $posSecond = strpos($html, 'Second');
        $posThird = strpos($html, 'Third');

        $this->assertNotFalse($posFirst);
        $this->assertNotFalse($posSecond);
        $this->assertNotFalse($posThird);
        $this->assertLessThan($posSecond, $posFirst);
        $this->assertLessThan($posThird, $posSecond);
    }

    /**
     * Test nested blocks are rendered correctly
     */
    public function test_nested_blocks_are_rendered_correctly(): void
    {
        $site = Site::factory()->create([
            'slug' => 'test-site',
            'is_published' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'is_published' => true,
            'is_home' => true,
        ]);

        // Create parent container
        $container = PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'container',
            'parent_id' => null,
            'is_visible' => true,
            'position' => 0,
        ]);

        // Create child block
        PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Nested content'],
            'parent_id' => $container->id,
            'is_visible' => true,
            'position' => 0,
        ]);

        $response = $this->get("/sites/{$site->slug}");

        $response->assertStatus(200)
            ->assertSee('Nested content');
    }
}
