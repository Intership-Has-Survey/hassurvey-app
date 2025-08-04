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
