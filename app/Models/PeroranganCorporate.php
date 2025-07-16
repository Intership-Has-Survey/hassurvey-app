<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // PERBAIKAN: Harus extends Pivot, bukan Model
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

// PERBAIKAN: Nama kelas diubah menjadi PascalCase dan harus extends Pivot
class PeroranganCorporate extends Pivot
{
    // PERBAIKAN: Gunakan SoftDeletes karena ada di migrasi, tapi hapus HasUuids dan HasFactory
    use SoftDeletes;

    // Memberitahu Eloquent bahwa ini adalah tabel pivot
    protected $table = 'perorangan_corporate';

    protected static function booted(): void
    {
        // Model event ini akan berjalan secara otomatis SEBELUM data pivot baru disimpan
        static::creating(function ($pivot) {
            // Jika user_id belum diisi, isi dengan ID user yang sedang login
            if (Auth::check() && !$pivot->user_id) {
                $pivot->user_id = Auth::id();
            }
        });
    }
}
