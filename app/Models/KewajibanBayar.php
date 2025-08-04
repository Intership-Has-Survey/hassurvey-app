<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KewajibanBayar extends Model
{
    use HasUuids;
    protected $table = 'kewajiban_bayars';

    protected $fillable = [
        'operasional_id',
        'penerima_operasional_id',
        'nama',
        'deskripsi',
        'nominal',
        'bukti',
    ];

    public function operasional(): BelongsTo
    {
        return $this->belongsTo(Operasional::class);
    }

    public function penerimaOperasional(): BelongsTo
    {
        return $this->belongsTo(PenerimaOperasional::class);
    }
}
