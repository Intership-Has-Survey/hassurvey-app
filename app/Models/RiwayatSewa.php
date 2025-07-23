<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Custom Pivot Model for riwayat_sewa table
 */
class RiwayatSewa extends Pivot
{
    use HasUuids;

    protected $table = 'riwayat_sewa'; // Pastikan nama tabel pivot sudah benar
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

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

    public function daftarAlat()
    {
        return $this->belongsTo(DaftarAlat::class, 'daftar_alat_id');
    }

    public function sewa()
    {
        return $this->belongsTo(Sewa::class, 'sewa_id');
    }
}
