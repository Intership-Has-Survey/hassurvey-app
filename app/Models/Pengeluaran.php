<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Pengeluaran extends Model
{
    use HasUuids;
    protected $table = 'pengeluarans';

    protected $fillable = [
        'tabungan_id',
        'tanggal',
        'jumlah',
        'deskripsi',
        'pengeluaranable_id',
        'pengeluaranable_type',
    ];

    public function tabungan(): BelongsTo
    {
        return $this->belongsTo(Tabungan::class);
    }

    public function pengeluaranable(): MorphTo
    {
        return $this->morphTo();
    }
}
