<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Pemilik;
use App\Models\User;

class PemilikPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Pemilik');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('view Pemilik');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Pemilik');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('update Pemilik');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('delete Pemilik');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Pemilik');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('restore Pemilik');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Pemilik');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('replicate Pemilik');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Pemilik');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pemilik $pemilik): bool
    {
        return $user->checkPermissionTo('force-delete Pemilik');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Pemilik');
    }
}
