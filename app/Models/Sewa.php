<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Iluminate\Database\Eloquent\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Sewa extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;
    protected $table = 'sewa';
    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'riwayat_sewa', 'sewa_id', 'daftar_alat_id')
            ->using(AlatSewa::class)
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

        static::updated(function ($sewa) {
            if ($sewa->is_locked) {
                $sewa->daftarAlat()->each(function ($alat) use ($sewa) {
                    $relationManager = app(\App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager::class);
                    // Remove call to setOwnerRecord as method does not exist
                    $relationManager->perhitunganFinal($alat, $sewa);
                });
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'sewa_id');
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan(): BelongsToMany
    {
        return $this->belongsToMany(Perorangan::class, 'sewa_perorangan')
            ->withPivot('perorangan_id', 'sewa_id', 'peran')
            ->withTimestamps();
    }

    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class);
    }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
    }

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // Use null-safe access to prevent undefined array key errors
                if (isset($attributes['is_locked']) && $attributes['is_locked']) {
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'sales_id',
                'judul',
                'tgl_mulai',
                'tgl_selesai',
                'rentang',
                'provinsi',
                'kota',
                'kecamatan',
                'desa',
                'detail_alamat',
                'harga_perkiraan',
                'harga_real',
                'harga_fix',
                'status',
                'needs_replacement',
                'is_locked',
                'created_at',
                'updated_at',
                'corporate_id',
                'user_id',
                'deleted_at',
                'company_id',
            ])
            ->logOnlyDirty()
            ->useLogName('Sewa');
    }
}
