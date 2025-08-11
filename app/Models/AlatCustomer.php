<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatCustomer extends Model
{
    //
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function kalibrasis()
    {
        return $this->belongsToMany(Kalibrasi::class, 'detail_kalibrasis')
            ->using(DetailKalibrasi::class)
            ->withPivot(['tgl_masuk', 'tgl_stiker_kalibrasi', 'tgl_keluar', 'status'])
            ->withTimestamps();
    }
    public function jenisalat()
    {
        return $this->belongsTo(JenisAlat::class, 'jenis_alat_id');
    }

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function customer()
    {
        return $this->belongsTo(Corporate::class);
    }

    public function getSearchableColumnNames(): array
    {
        return ['nomor_seri']; // pastikan ini kolom yang valid
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan()
    {
        return $this->belongsToMany(Perorangan::class, 'alat_customers_perorangan', 'alat_customers_id', 'perorangan_id')
            ->withPivot('alat_customers_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
