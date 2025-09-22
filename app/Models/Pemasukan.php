<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemasukan extends Model
{
    protected $fillable = [
        'tabungan_id',
        'tanggal',
        'jumlah',
        'deskripsi',
    ];

    public function tabungan(): BelongsTo
    {
        return $this->belongsTo(Tabungan::class);
    }

    public function visiMati(): BelongsTo
    {
        return $this->tabungan()->first()?->visiMati();
    }
}
