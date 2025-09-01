<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Sewa;
use App\Models\User;

class SewaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Sewa');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('view Sewa');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Sewa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('update Sewa');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('delete Sewa');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Sewa');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('restore Sewa');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Sewa');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('replicate Sewa');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Sewa');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sewa $sewa): bool
    {
        return $user->checkPermissionTo('force-delete Sewa');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Sewa');
    }
}
