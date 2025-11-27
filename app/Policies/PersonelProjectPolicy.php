<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PersonelProject;
use App\Models\User;

class PersonelProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PersonelProject');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('view PersonelProject');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PersonelProject');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('update PersonelProject');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('delete PersonelProject');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PersonelProject');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('restore PersonelProject');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PersonelProject');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('replicate PersonelProject');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PersonelProject');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonelProject $personelproject): bool
    {
        return $user->checkPermissionTo('force-delete PersonelProject');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PersonelProject');
    }
}
