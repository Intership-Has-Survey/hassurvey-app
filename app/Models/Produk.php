<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Produk extends Model
{
    //
    use HasUuids, SoftDeletes, logsActivity;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisAlat()
    {
        return $this->belongsTo(JenisAlat::class);
    }

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('Produk');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
