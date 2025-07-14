<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

/**
 * Custom Pivot Model for riwayat_sewa table
 */
class RiwayatSewa extends Pivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Model event ini akan berjalan secara otomatis SEBELUM data pivot baru disimpan
        static::creating(function ($pivot) {
            // Jika user_id belum diisi, isi dengan ID user yang sedang login
            if (Auth::check()) {
                $pivot->user_id = Auth::id();
            }

            // SOLUSI: Menghapus logika untuk mengisi kolom 'status' yang sudah tidak ada.
        });
    }
}
