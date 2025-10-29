<?php

namespace App\Models;

use App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kalibrasi extends Model
{
    //
    use HasUuids, LogsActivity, SoftDeletes;

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 'dalam_proses',
    ];

    public function customer()
    {
        return $this->belongsTo(Corporate::class);
    }

    // protected static function booted(): void
    // {
    //     static::creating(function ($pemilik) {
    //         if (!$pemilik->user_id && Auth::check()) {
    //             $pemilik->user_id = Auth::id();
    //         }
    //     });
    // }

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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'id',
                'nama',
                'corporate_id',
                'perorangan_id',
                'user_id',
                'harga',
                'status',
                'created_at',
                'updated_at',
                'company_id',
            ])
            ->logOnlyDirty()
            ->useLogName('Kalibrasi');
    }

    protected static function booted()
    {
        static::creating(function ($kalibrasi) {
            $tanggal = today()->format('Ymd');

            // Hi-tung berapa kalibrasi yang sudah ada di tanggal ini
            $countToday = Kalibrasi::whereDate('created_at', today()->toDateString())->count() + 1;

            // Format dengan 3 digit (001, 002, dst)
            $urutan = str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $kalibrasi->kode_kalibrasi = 'LKAL' .  $tanggal .  $urutan;
        });
    }
}
