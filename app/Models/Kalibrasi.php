<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Customer;

class Kalibrasi extends Model
{
    //
    use HasUuids;

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Corporate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alatCustomers()
    {
        return $this->belongsToMany(AlatCustomer::class, 'detail_kalibrasis')
            ->using(DetailKalibrasi::class)
            ->withPivot(['tgl_masuk', 'tgl_stiker_kalibrasi', 'tgl_keluar', 'status'])
            ->withTimestamps();
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan()
    {
        return $this->belongsToMany(Perorangan::class, 'kalibrasi_perorangan')
            ->withPivot('perorangan_id', 'kalibrasi_id')
            ->withTimestamps();
    }

    public function pengajuanDanas()
    {
        return $this->hasMany(PengajuanDana::class, 'sewa_id');
    }
}
