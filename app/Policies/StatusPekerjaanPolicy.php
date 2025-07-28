<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\StatusPekerjaan;
use App\Models\User;

class StatusPekerjaanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any StatusPekerjaan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('view StatusPekerjaan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create StatusPekerjaan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('update StatusPekerjaan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('delete StatusPekerjaan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any StatusPekerjaan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('restore StatusPekerjaan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any StatusPekerjaan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('replicate StatusPekerjaan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder StatusPekerjaan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StatusPekerjaan $statuspekerjaan): bool
    {
        return $user->checkPermissionTo('force-delete StatusPekerjaan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any StatusPekerjaan');
    }
}
