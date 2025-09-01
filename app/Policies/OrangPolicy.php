<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Orang;
use App\Models\User;

class OrangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Orang');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('view Orang');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Orang');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('update Orang');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('delete Orang');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Orang');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('restore Orang');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Orang');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('replicate Orang');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Orang');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Orang $orang): bool
    {
        return $user->checkPermissionTo('force-delete Orang');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Orang');
    }
}
