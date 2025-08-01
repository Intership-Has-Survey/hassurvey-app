<?php

namespace App\Models;

use App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(PengajuanDana::class);
    }
}
