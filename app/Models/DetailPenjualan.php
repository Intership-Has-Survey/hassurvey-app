<?php

namespace App\Models;

use Penjualan;
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
        'daftar_alat_id',
        'jumlah',
        'harga_satuan',
        'subtotal_item',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function daftarAlat(): BelongsTo
    {
        return $this->belongsTo(DaftarAlat::class);
    }
}
