<?php

namespace App\Models;

use App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;
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

    protected static function booted(): void
    {
        static::creating(function ($pemilik) {
            if (!$pemilik->user_id && Auth::check()) {
                $pemilik->user_id = Auth::id();
            }
        });
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
        return $this->belongsToMany(Perorangan::class, 'kalibrasi_perorangan')
            ->withPivot('kalibrasi_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function pengajuanDanas()
    {
        return $this->hasMany(PengajuanDana::class);
    }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
    }
}
