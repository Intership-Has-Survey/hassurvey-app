<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bangunan;
use App\Models\User;

class BangunanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Bangunan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('view Bangunan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Bangunan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('update Bangunan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('delete Bangunan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Bangunan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('restore Bangunan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Bangunan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('replicate Bangunan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Bangunan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bangunan $bangunan): bool
    {
        return $user->checkPermissionTo('force-delete Bangunan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Bangunan');
    }
}
