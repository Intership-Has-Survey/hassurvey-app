<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\level;
use App\Models\User;

class levelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any level');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, level $level): bool
    {
        return $user->checkPermissionTo('view level');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create level');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, level $level): bool
    {
        return $user->checkPermissionTo('update level');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, level $level): bool
    {
        return $user->checkPermissionTo('delete level');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any level');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, level $level): bool
    {
        return $user->checkPermissionTo('restore level');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any level');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, level $level): bool
    {
        return $user->checkPermissionTo('replicate level');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder level');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, level $level): bool
    {
        return $user->checkPermissionTo('force-delete level');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any level');
    }
}
