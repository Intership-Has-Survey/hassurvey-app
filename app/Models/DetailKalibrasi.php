<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DetailKalibrasi extends Pivot
{
    //
    use HasUuids;
    protected $table = 'detail_kalibrasis';
    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 'belum_dikerjakan',
    ];

    public function alatCustomer()
    {
        return $this->belongsTo(AlatCustomer::class, 'alat_customer_id');
    }

    public function kalibrasi()
    {
        return $this->belongsTo(Kalibrasi::class, 'kalibrasi_id');
    }
}
