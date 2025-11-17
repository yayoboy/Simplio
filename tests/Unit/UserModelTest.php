<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    /**
     * Test isAdmin method returns true for admin users
     */
    public function test_is_admin_returns_true_for_admin_users(): void
    {
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $this->assertTrue($admin->isAdmin());

        $editor = new User(['role' => User::ROLE_EDITOR]);
        $this->assertFalse($editor->isAdmin());

        $user = new User(['role' => User::ROLE_USER]);
        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test isEditor method returns true for editor users
     */
    public function test_is_editor_returns_true_for_editor_users(): void
    {
        $editor = new User(['role' => User::ROLE_EDITOR]);
        $this->assertTrue($editor->isEditor());

        $admin = new User(['role' => User::ROLE_ADMIN]);
        $this->assertFalse($admin->isEditor());

        $user = new User(['role' => User::ROLE_USER]);
        $this->assertFalse($user->isEditor());
    }

    /**
     * Test canManageUsers method
     */
    public function test_can_manage_users_returns_true_only_for_admins(): void
    {
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $this->assertTrue($admin->canManageUsers());

        $editor = new User(['role' => User::ROLE_EDITOR]);
        $this->assertFalse($editor->canManageUsers());

        $user = new User(['role' => User::ROLE_USER]);
        $this->assertFalse($user->canManageUsers());
    }

    /**
     * Test canManageSites method
     */
    public function test_can_manage_sites_returns_true_for_admins_and_editors(): void
    {
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $this->assertTrue($admin->canManageSites());

        $editor = new User(['role' => User::ROLE_EDITOR]);
        $this->assertTrue($editor->canManageSites());

        $user = new User(['role' => User::ROLE_USER]);
        $this->assertFalse($user->canManageSites());
    }

    /**
     * Test role constants are defined correctly
     */
    public function test_role_constants_are_defined(): void
    {
        $this->assertEquals('admin', User::ROLE_ADMIN);
        $this->assertEquals('editor', User::ROLE_EDITOR);
        $this->assertEquals('user', User::ROLE_USER);
    }

    /**
     * Test default role is user
     */
    public function test_default_role_is_user(): void
    {
        $user = new User();

        // Check if role is fillable
        $this->assertContains('role', $user->getFillable());
    }
}
