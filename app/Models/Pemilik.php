<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Pemilik extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $table = 'pemilik';

    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->hasMany(DaftarAlat::class, 'pemilik_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($pemilik) {
            if (!$pemilik->user_id && Auth::check()) {
                $pemilik->user_id = Auth::id();
            }
        });
    }

    public function riwayatSewaAlat(): HasManyThrough
    {
        /**
         * Method ini mengambil RiwayatSewa MELALUI DaftarAlat.
         * Laravel secara otomatis akan menghubungkan:
         * pemilik.id -> daftar_alat.pemilik_id
         * daftar_alat.id -> riwayat_sewa.daftar_alat_id
         */
        return $this->hasManyThrough(RiwayatSewa::class, DaftarAlat::class);
    }
}
