<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\LevelStep;
use App\Models\User;

class LevelStepPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any LevelStep');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('view LevelStep');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create LevelStep');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('update LevelStep');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('delete LevelStep');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any LevelStep');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('restore LevelStep');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any LevelStep');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('replicate LevelStep');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder LevelStep');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LevelStep $levelstep): bool
    {
        return $user->checkPermissionTo('force-delete LevelStep');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any LevelStep');
    }
}
