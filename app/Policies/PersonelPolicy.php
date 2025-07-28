<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Personel;
use App\Models\User;

class PersonelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Personel');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('view Personel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Personel');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('update Personel');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('delete Personel');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Personel');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('restore Personel');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Personel');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('replicate Personel');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Personel');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Personel $personel): bool
    {
        return $user->checkPermissionTo('force-delete Personel');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Personel');
    }
}
