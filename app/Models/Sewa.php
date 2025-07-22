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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Sewa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $table = 'sewa';
    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'riwayat_sewa', 'sewa_id', 'daftar_alat_id')
            ->using(RiwayatSewa::class)
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa_alat', 'user_id'])
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function ($sewa) {
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

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($attributes['is_locked']) {
                    return 'Selesai';
                }

                if (isset($attributes['tgl_selesai'])) {
                    $tglSelesai = Carbon::parse($attributes['tgl_selesai']);
                    if (Carbon::today()->gt($tglSelesai)) {
                        return 'Jatuh Tempo';
                    }
                }

                return 'Belum Selesai';
            },
        );
    }

    public function canAddTools(): bool
    {
        if ($this->is_locked) {
            return false;
        }

        $totalAlat = $this->daftarAlat()->count();
        if ($totalAlat === 0) {
            return true;
        }

        $alatDikembalikan = $this->daftarAlat()->wherePivotNotNull('tgl_masuk')->count();
        $butuhPengganti = $this->daftarAlat()->wherePivot('needs_replacement', true)->count();

        if ($totalAlat === $alatDikembalikan && $butuhPengganti === 0) {
            return false;
        }

        return true;
    }
}
