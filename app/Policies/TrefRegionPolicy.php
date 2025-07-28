<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TrefRegion;
use App\Models\User;

class TrefRegionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TrefRegion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('view TrefRegion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TrefRegion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('update TrefRegion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('delete TrefRegion');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TrefRegion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('restore TrefRegion');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TrefRegion');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('replicate TrefRegion');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TrefRegion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrefRegion $trefregion): bool
    {
        return $user->checkPermissionTo('force-delete TrefRegion');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TrefRegion');
    }
}
