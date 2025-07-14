<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PengajuanDana extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function detailPengajuans(): HasMany
    {
        return $this->hasMany(DetailPengajuan::class);
    }


    public function transaksiPembayarans(): HasMany
    {
        return $this->hasMany(TransaksiPembayaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['judul_pengajuan', 'status', 'deskripsi_pengajuan', 'nama_bank', 'nomor_rekening', 'nama_pemilik_rekening'])
            ->logOnlyDirty()
            ->useLogName('Pengajuan');
    }
}
