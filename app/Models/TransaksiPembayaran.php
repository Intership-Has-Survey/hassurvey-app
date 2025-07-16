<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPembayaran extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function pengajuanDana(): BelongsTo
    {
        return $this->belongsTo(PengajuanDana::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
