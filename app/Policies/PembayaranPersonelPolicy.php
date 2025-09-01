<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PembayaranPersonel;
use App\Models\User;

class PembayaranPersonelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PembayaranPersonel');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('view PembayaranPersonel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PembayaranPersonel');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('update PembayaranPersonel');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('delete PembayaranPersonel');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PembayaranPersonel');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('restore PembayaranPersonel');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PembayaranPersonel');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('replicate PembayaranPersonel');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PembayaranPersonel');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PembayaranPersonel $pembayaranpersonel): bool
    {
        return $user->checkPermissionTo('force-delete PembayaranPersonel');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PembayaranPersonel');
    }
}
