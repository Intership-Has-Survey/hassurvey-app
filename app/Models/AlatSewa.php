<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Custom Pivot Model for riwayat_sewa table
 */

class AlatSewa extends Pivot
{
    use HasUuids;

    protected $table = 'riwayat_sewa';
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
        static::creating(function ($pivot) {
            if (Auth::check()) {
                $pivot->user_id = Auth::id();
            }
        });

        static::saved(function ($pivot) {
            $alat = DaftarAlat::find($pivot->daftar_alat_id);
            // dd([$alat, $pivot]);

            if ($pivot->tgl_masuk !== null) {
                // alat sudah dikembalikan
                $alat->status = 1;
            } else {
                // alat sedang disewa
                $alat->status = 0;
            }

            $alat->save();
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

    public function getApakahAttribute()
    {
        // Jika ada tgl_masuk, alat sudah dikembalikan (Tersedia)
        if (!empty($this->tgl_masuk)) {
            return 'Tersedia';
        }
        // Jika tidak ada tgl_masuk tapi ada tgl_keluar, alat masih dipinjam
        else if (!empty($this->tgl_keluar)) {
            return 'Terpakai';
        }
        // Default status
        return 'Menunggu';
    }
}
