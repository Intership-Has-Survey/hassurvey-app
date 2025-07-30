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

    protected $attributes = [
        'status' => 'dalam_proses',
    ];

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
        return $this->hasMany(DetailKalibrasi::class);
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan()
    {
        return $this->belongsTo(Perorangan::class, 'perorangan_id');
    }

    public function pengajuanDanas()
    {
        return $this->hasMany(PengajuanDana::class, 'sewa_id');
    }

    // public function alatCustomers()
    // {
    //     return $this->belongsToMany(AlatCustomer::class, 'riwayat_sewa', 'sewa_id', 'daftar_alat_id')
    //         ->using(RiwayatSewa::class)
    //         ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa_alat', 'user_id'])
    //         ->withTimestamps();
    // }

    // public function alatCustomers()
    // {
    //     return $this->belongsToMany(AlatCustomer::class, 'detail_kalibrasis')
    //         ->using(DetailKalibrasi::class)
    //         ->withPivot(['tgl_masuk', 'tgl_stiker_kalibrasi', 'tgl_keluar', 'status'])
    //         ->withTimestamps();
    // }
}
