<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\RiwayatSewa;
use App\Models\User;

class RiwayatSewaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any RiwayatSewa');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('view RiwayatSewa');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create RiwayatSewa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('update RiwayatSewa');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('delete RiwayatSewa');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any RiwayatSewa');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('restore RiwayatSewa');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any RiwayatSewa');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('replicate RiwayatSewa');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder RiwayatSewa');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RiwayatSewa $riwayatsewa): bool
    {
        return $user->checkPermissionTo('force-delete RiwayatSewa');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any RiwayatSewa');
    }
}
