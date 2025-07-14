<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanDana extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

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
}
