<?php

namespace App\Policies;

use App\Models\Theme;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThemePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Theme $theme): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Theme $theme): bool
    {
        // Can update if it's their theme or if they're editing a global theme
        return !$theme->is_global && $theme->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Theme $theme): bool
    {
        // Cannot delete global themes, only custom ones
        return !$theme->is_global && $theme->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Theme $theme): bool
    {
        return !$theme->is_global && $theme->created_by === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Theme $theme): bool
    {
        return !$theme->is_global && $theme->created_by === $user->id;
    }
}
