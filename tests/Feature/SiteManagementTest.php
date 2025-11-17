<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SiteManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can list sites
     */
    public function test_authenticated_user_can_list_sites(): void
    {
        $user = User::factory()->create();
        Site::factory()->count(3)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'slug', 'domain', 'is_published', 'created_at', 'updated_at'],
            ]);
    }

    /**
     * Test authenticated user can create a site
     */
    public function test_authenticated_user_can_create_site(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $siteData = [
            'name' => 'Test Site',
            'slug' => 'test-site',
            'domain' => 'testsite.com',
            'description' => 'A test site',
        ];

        $response = $this->postJson('/api/sites', $siteData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Site created successfully',
                'data' => [
                    'name' => 'Test Site',
                    'slug' => 'test-site',
                    'domain' => 'testsite.com',
                    'is_published' => false,
                ],
            ]);

        $this->assertDatabaseHas('sites', [
            'name' => 'Test Site',
            'slug' => 'test-site',
            'domain' => 'testsite.com',
        ]);
    }

    /**
     * Test site creation requires valid data
     */
    public function test_site_creation_requires_valid_data(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // Test missing required fields
        $response = $this->postJson('/api/sites', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);

        // Test invalid slug format
        $response = $this->postJson('/api/sites', [
            'name' => 'Test Site',
            'slug' => 'Invalid Slug!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test site slug must be unique
     */
    public function test_site_slug_must_be_unique(): void
    {
        $user = User::factory()->create();
        Site::factory()->create(['slug' => 'existing-site']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sites', [
            'name' => 'Another Site',
            'slug' => 'existing-site',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test authenticated user can view a site
     */
    public function test_authenticated_user_can_view_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/sites/{$site->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'slug' => $site->slug,
                ],
            ]);
    }

    /**
     * Test authenticated user can update a site
     */
    public function test_authenticated_user_can_update_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        $updatedData = [
            'name' => 'Updated Site Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/sites/{$site->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Site updated successfully',
                'data' => [
                    'id' => $site->id,
                    'name' => 'Updated Site Name',
                    'description' => 'Updated description',
                ],
            ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'name' => 'Updated Site Name',
        ]);
    }

    /**
     * Test authenticated user can delete a site
     */
    public function test_authenticated_user_can_delete_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/sites/{$site->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Site deleted successfully']);

        $this->assertDatabaseMissing('sites', ['id' => $site->id]);
    }

    /**
     * Test authenticated user can publish a site
     */
    public function test_authenticated_user_can_publish_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['is_published' => false]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/sites/{$site->id}/publish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Site published successfully',
                'data' => [
                    'id' => $site->id,
                    'is_published' => true,
                ],
            ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'is_published' => true,
        ]);
    }

    /**
     * Test authenticated user can unpublish a site
     */
    public function test_authenticated_user_can_unpublish_site(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['is_published' => true]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/sites/{$site->id}/unpublish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Site unpublished successfully',
                'data' => [
                    'id' => $site->id,
                    'is_published' => false,
                ],
            ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'is_published' => false,
        ]);
    }

    /**
     * Test unauthenticated users cannot access site management
     */
    public function test_unauthenticated_users_cannot_access_site_management(): void
    {
        $response = $this->getJson('/api/sites');
        $response->assertStatus(401);

        $response = $this->postJson('/api/sites', []);
        $response->assertStatus(401);

        $site = Site::factory()->create();

        $response = $this->getJson("/api/sites/{$site->id}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/sites/{$site->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/sites/{$site->id}");
        $response->assertStatus(401);
    }

    /**
     * Test site returns 404 for non-existent site
     */
    public function test_returns_404_for_non_existent_site(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sites/99999');
        $response->assertStatus(404);
    }
}
