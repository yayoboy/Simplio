<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can list all users
     */
    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->count(5)->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
                ],
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Test non-admin cannot list users
     */
    public function test_non_admin_cannot_list_users(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized. Admin access required.']);
    }

    /**
     * Test admin can create a user
     */
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Sanctum::actingAs($admin);

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => User::ROLE_EDITOR,
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully',
                'data' => [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                    'role' => User::ROLE_EDITOR,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => User::ROLE_EDITOR,
        ]);
    }

    /**
     * Test user creation validation
     */
    public function test_user_creation_requires_valid_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Sanctum::actingAs($admin);

        // Test missing required fields
        $response = $this->postJson('/api/users', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);

        // Test invalid email
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'role' => User::ROLE_USER,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test short password
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'role' => User::ROLE_USER,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Test invalid role
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'invalid_role',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    /**
     * Test admin can update a user
     */
    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Sanctum::actingAs($admin);

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => User::ROLE_EDITOR,
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com',
                    'role' => User::ROLE_EDITOR,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => User::ROLE_EDITOR,
        ]);
    }

    /**
     * Test admin can view a single user
     */
    public function test_admin_can_view_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
    }

    /**
     * Test admin can delete a user
     */
    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test admin cannot delete themselves
     */
    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'You cannot delete your own account']);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Test fetching available roles
     */
    public function test_admin_can_fetch_roles(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users/roles');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['value' => User::ROLE_ADMIN, 'label' => 'Administrator'],
                    ['value' => User::ROLE_EDITOR, 'label' => 'Editor'],
                    ['value' => User::ROLE_USER, 'label' => 'User'],
                ],
            ]);
    }

    /**
     * Test searching users by name
     */
    public function test_admin_can_search_users_by_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users?search=John');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertTrue(
            collect($data)->contains('name', 'John Doe')
        );
    }

    /**
     * Test filtering users by role
     */
    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->count(3)->create(['role' => User::ROLE_EDITOR]);
        User::factory()->count(2)->create(['role' => User::ROLE_USER]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users?role=' . User::ROLE_EDITOR);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(3, count($data));
        $this->assertTrue(
            collect($data)->every(fn($user) => $user['role'] === User::ROLE_EDITOR)
        );
    }

    /**
     * Test unauthenticated users cannot access user endpoints
     */
    public function test_unauthenticated_users_cannot_access_user_endpoints(): void
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);

        $response = $this->postJson('/api/users', []);
        $response->assertStatus(401);
    }
}
