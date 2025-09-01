<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\VisiMati;
use App\Models\User;

class VisiMatiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any VisiMati');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('view VisiMati');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create VisiMati');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('update VisiMati');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('delete VisiMati');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any VisiMati');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('restore VisiMati');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any VisiMati');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('replicate VisiMati');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder VisiMati');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VisiMati $visimati): bool
    {
        return $user->checkPermissionTo('force-delete VisiMati');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any VisiMati');
    }
}
