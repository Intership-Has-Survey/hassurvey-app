<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Corporate;
use App\Models\User;

class CorporatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Corporate');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('view Corporate');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Corporate');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('update Corporate');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('delete Corporate');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Corporate');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('restore Corporate');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Corporate');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('replicate Corporate');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Corporate');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Corporate $corporate): bool
    {
        return $user->checkPermissionTo('force-delete Corporate');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Corporate');
    }
}
