<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    use HasUuids;

    protected $fillable = ['name'];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class);
    // }

    public function daftarAlats()
    {
        return $this->hasMany(DaftarAlat::class);
    }

    public function personels()
    {
        return $this->hasMany(Personel::class);
    }

    public function pemiliks()
    {
        return $this->hasMany(Pemilik::class);
    }

    public function sales()
    {
        return $this->hasMany(Pemilik::class);
    }

    public function alatCustomers()
    {
        return $this->hasMany(AlatCustomer::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function sewas()
    {
        return $this->hasMany(Sewa::class);
    }

    public function kalibrasis()
    {
        return $this->hasMany(Kalibrasi::class);
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function corporates()
    {
        return $this->hasMany(Corporate::class);
    }

    public function perorangans()
    {
        return $this->hasMany(Perorangan::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function transaksiPembayarans()
    {
        return $this->hasMany(TransaksiPembayaran::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
