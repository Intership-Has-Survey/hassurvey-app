<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PeroranganCorporate;
use App\Models\User;

class PeroranganCorporatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PeroranganCorporate');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('view PeroranganCorporate');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PeroranganCorporate');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('update PeroranganCorporate');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('delete PeroranganCorporate');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PeroranganCorporate');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('restore PeroranganCorporate');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PeroranganCorporate');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('replicate PeroranganCorporate');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PeroranganCorporate');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PeroranganCorporate $perorangancorporate): bool
    {
        return $user->checkPermissionTo('force-delete PeroranganCorporate');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PeroranganCorporate');
    }
}
