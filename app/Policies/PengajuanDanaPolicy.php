<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PengajuanDana;
use App\Models\User;

class PengajuanDanaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PengajuanDana');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('view PengajuanDana');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PengajuanDana');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('update PengajuanDana');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('delete PengajuanDana');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PengajuanDana');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('restore PengajuanDana');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PengajuanDana');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('replicate PengajuanDana');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PengajuanDana');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PengajuanDana $pengajuandana): bool
    {
        return $user->checkPermissionTo('force-delete PengajuanDana');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PengajuanDana');
    }
}
