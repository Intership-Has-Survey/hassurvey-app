<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\KewajibanBayar;
use App\Models\User;

class KewajibanBayarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any KewajibanBayar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('view KewajibanBayar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create KewajibanBayar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('update KewajibanBayar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('delete KewajibanBayar');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any KewajibanBayar');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('restore KewajibanBayar');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any KewajibanBayar');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('replicate KewajibanBayar');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder KewajibanBayar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KewajibanBayar $kewajibanbayar): bool
    {
        return $user->checkPermissionTo('force-delete KewajibanBayar');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any KewajibanBayar');
    }
}
