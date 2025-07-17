<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Iluminate\Database\Eloquent\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class Sewa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $table = 'sewa';
    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'riwayat_sewa', 'sewa_id', 'daftar_alat_id')
            ->using(RiwayatSewa::class)
            // SOLUSI: Mengganti 'biaya_sewa' menjadi 'biaya_sewa_alat' agar sesuai dengan migrasi
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa_alat', 'user_id'])
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function ($sewa) {
            // Atur user_id jika belum ada dan user sedang login
            if (!$sewa->user_id && Auth::check()) {
                $sewa->user_id = Auth::id();
            }
        });
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'sewa_id');
    }

    public function customer()
    {
        return $this->morphTo();
    }

    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class, 'sewa_id');
    }
}
