<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KewajibanBayar extends Model
{
    protected $table = 'kewajiban_bayars';

    protected $fillable = [
        'operasional_id',
        'bangunan_id',
        'nama',
        'deskripsi',
        'nominal',
        'bukti',
    ];

    public function operasional(): BelongsTo
    {
        return $this->belongsTo(Operasional::class);
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class);
    }
}
