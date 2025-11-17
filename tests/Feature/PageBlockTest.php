<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageBlockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can list blocks for a page
     */
    public function test_authenticated_user_can_list_blocks(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        PageBlock::factory()->count(3)->create([
            'page_id' => $page->id,
            'parent_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/pages/{$page->id}/blocks");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test authenticated user can create a block
     */
    public function test_authenticated_user_can_create_block(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $blockData = [
            'type' => 'text',
            'content' => ['text' => 'Hello World'],
            'properties' => ['alignment' => 'center'],
            'position' => 0,
        ];

        $response = $this->postJson("/api/pages/{$page->id}/blocks", $blockData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Block created successfully',
                'data' => [
                    'type' => 'text',
                    'page_id' => $page->id,
                    'is_visible' => true,
                ],
            ]);

        $this->assertDatabaseHas('page_blocks', [
            'page_id' => $page->id,
            'type' => 'text',
        ]);
    }

    /**
     * Test block creation requires valid type
     */
    public function test_block_creation_requires_valid_type(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        // Test missing type
        $response = $this->postJson("/api/pages/{$page->id}/blocks", []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);

        // Test invalid type
        $response = $this->postJson("/api/pages/{$page->id}/blocks", [
            'type' => 'invalid_type',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test authenticated user can update a block
     */
    public function test_authenticated_user_can_update_block(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);
        $block = PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text',
            'content' => ['text' => 'Original text'],
        ]);

        Sanctum::actingAs($user);

        $updatedData = [
            'content' => ['text' => 'Updated text'],
            'properties' => ['fontSize' => 'large'],
        ];

        $response = $this->putJson("/api/blocks/{$block->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Block updated successfully',
            ]);

        $block->refresh();
        $this->assertEquals('Updated text', $block->content['text']);
    }

    /**
     * Test authenticated user can toggle block visibility
     */
    public function test_authenticated_user_can_toggle_block_visibility(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);
        $block = PageBlock::factory()->create([
            'page_id' => $page->id,
            'is_visible' => true,
        ]);

        Sanctum::actingAs($user);

        // Hide block
        $response = $this->putJson("/api/blocks/{$block->id}", [
            'is_visible' => false,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $block->id,
            'is_visible' => false,
        ]);

        // Show block
        $response = $this->putJson("/api/blocks/{$block->id}", [
            'is_visible' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $block->id,
            'is_visible' => true,
        ]);
    }

    /**
     * Test authenticated user can delete a block
     */
    public function test_authenticated_user_can_delete_block(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);
        $block = PageBlock::factory()->create(['page_id' => $page->id]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/blocks/{$block->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Block deleted successfully']);

        $this->assertDatabaseMissing('page_blocks', ['id' => $block->id]);
    }

    /**
     * Test authenticated user can reorder blocks
     */
    public function test_authenticated_user_can_reorder_blocks(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        $block1 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 0,
        ]);

        $block2 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 1,
        ]);

        $block3 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 2,
        ]);

        Sanctum::actingAs($user);

        // Reorder: move block3 to first position
        $response = $this->postJson('/api/blocks/reorder', [
            'blocks' => [
                ['id' => $block3->id, 'position' => 0],
                ['id' => $block1->id, 'position' => 1],
                ['id' => $block2->id, 'position' => 2],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Blocks reordered successfully']);

        // Verify new positions
        $this->assertDatabaseHas('page_blocks', [
            'id' => $block3->id,
            'position' => 0,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $block1->id,
            'position' => 1,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $block2->id,
            'position' => 2,
        ]);
    }

    /**
     * Test blocks are returned in correct order
     */
    public function test_blocks_are_returned_in_correct_order(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        $block1 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 2,
            'type' => 'text',
        ]);

        $block2 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 0,
            'type' => 'heading',
        ]);

        $block3 = PageBlock::factory()->create([
            'page_id' => $page->id,
            'position' => 1,
            'type' => 'image',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/pages/{$page->id}/blocks");

        $response->assertStatus(200);

        $blocks = $response->json();

        // Should be ordered by position: block2 (0), block3 (1), block1 (2)
        $this->assertEquals($block2->id, $blocks[0]['id']);
        $this->assertEquals($block3->id, $blocks[1]['id']);
        $this->assertEquals($block1->id, $blocks[2]['id']);
    }

    /**
     * Test can create nested blocks with parent_id
     */
    public function test_can_create_nested_blocks(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        // Create parent container block
        $parent = PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'container',
            'parent_id' => null,
        ]);

        Sanctum::actingAs($user);

        // Create child block
        $response = $this->postJson("/api/pages/{$page->id}/blocks", [
            'type' => 'text',
            'parent_id' => $parent->id,
            'content' => ['text' => 'Child block'],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('page_blocks', [
            'page_id' => $page->id,
            'type' => 'text',
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * Test different block types can be created
     */
    public function test_different_block_types_can_be_created(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user);

        $blockTypes = [
            'text',
            'heading',
            'image',
            'gallery',
            'video',
            'html',
            'button',
            'divider',
            'spacer',
            'container',
            'columns',
        ];

        foreach ($blockTypes as $type) {
            $response = $this->postJson("/api/pages/{$page->id}/blocks", [
                'type' => $type,
                'content' => [],
            ]);

            $response->assertStatus(201);

            $this->assertDatabaseHas('page_blocks', [
                'page_id' => $page->id,
                'type' => $type,
            ]);
        }
    }

    /**
     * Test only root blocks (no parent) are returned by default
     */
    public function test_only_root_blocks_are_returned_by_default(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);

        // Create parent block
        $parent = PageBlock::factory()->create([
            'page_id' => $page->id,
            'parent_id' => null,
        ]);

        // Create child blocks
        PageBlock::factory()->count(3)->create([
            'page_id' => $page->id,
            'parent_id' => $parent->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/pages/{$page->id}/blocks");

        $response->assertStatus(200)
            ->assertJsonCount(1); // Only root block returned

        // Verify children are included in the relationship
        $block = $response->json()[0];
        $this->assertArrayHasKey('children', $block);
        $this->assertCount(3, $block['children']);
    }

    /**
     * Test unauthenticated users cannot access block management
     */
    public function test_unauthenticated_users_cannot_access_block_management(): void
    {
        $site = Site::factory()->create();
        $page = Page::factory()->create(['site_id' => $site->id]);
        $block = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this->getJson("/api/pages/{$page->id}/blocks");
        $response->assertStatus(401);

        $response = $this->postJson("/api/pages/{$page->id}/blocks", []);
        $response->assertStatus(401);

        $response = $this->putJson("/api/blocks/{$block->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/blocks/{$block->id}");
        $response->assertStatus(401);

        $response = $this->postJson('/api/blocks/reorder', []);
        $response->assertStatus(401);
    }
}
