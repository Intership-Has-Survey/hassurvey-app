<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PenerimaOperasional;
use App\Models\User;

class PenerimaOperasionalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PenerimaOperasional');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('view PenerimaOperasional');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PenerimaOperasional');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('update PenerimaOperasional');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('delete PenerimaOperasional');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PenerimaOperasional');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('restore PenerimaOperasional');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PenerimaOperasional');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('replicate PenerimaOperasional');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PenerimaOperasional');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PenerimaOperasional $penerimaoperasional): bool
    {
        return $user->checkPermissionTo('force-delete PenerimaOperasional');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PenerimaOperasional');
    }
}
