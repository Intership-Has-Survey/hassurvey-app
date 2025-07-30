<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPenjualan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'jenis_alat_id',
        'daftar_alat_id',
        'merk_id',
        'harga',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function daftarAlat(): BelongsTo
    {
        return $this->belongsTo(DaftarAlat::class);
    }

    public function jenisAlat(): BelongsTo
    {
        return $this->belongsTo(\App\Models\JenisAlat::class);
    }

    public function merk(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Merk::class);
    }
}
