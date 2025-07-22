<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class JenisAlat extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $table = 'jenis_alat';

    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->hasMany(DaftarAlat::class, 'jenis_alat_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($jenisAlat) {
            if (!$jenisAlat->user_id && Auth::check()) {
                $jenisAlat->user_id = Auth::id();
            }
        });
    }
}
