<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Merk;
use App\Models\User;

class MerkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Merk');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('view Merk');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Merk');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('update Merk');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('delete Merk');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Merk');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('restore Merk');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Merk');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('replicate Merk');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Merk');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Merk $merk): bool
    {
        return $user->checkPermissionTo('force-delete Merk');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Merk');
    }
}
