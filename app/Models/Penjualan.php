<?php

namespace App\Models;

use App\Models\DetailPenjualan;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Penjualan extends Model
{
    use HasFactory, HasUuids, LogsActivity, SoftDeletes;

    protected $table = 'penjualans';

    protected $guarded = [];

    protected $appends = ['total_items'];

    public function getTotalItemsAttribute(): string
    {
        return 'Rp ' . number_format($this->detailPenjualan->sum('harga'), 0, ',', '.');
    }

    public function getTotallItemsAttribute(): string
    {
        return $this->detailPenjualan->sum('harga');
    }

    protected $casts = [
        'tanggal_penjualan' => 'date',
    ];

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function perorangan(): BelongsToMany
    {
        return $this->belongsToMany(Perorangan::class, 'penjualan_perorangan')
            ->withPivot('penjualan_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function pengajuanDanas()
    {
        return $this->morphMany(PengajuanDana::class, 'pengajuanable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
    }

    public function getTotalPembayaranAttribute()
    {
        return $this->statusPembayaran()->sum('nilai');
    }


    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'id',
                'nama_penjualan',
                'tanggal_penjualan',
                'total_items',
                'catatan',
                'status_pembayaran',
                'user_id',
                'sales_id',
                'corporate_id',
            ])
            ->logOnlyDirty()
            ->useLogName('Penjualan');
    }

    protected static function booted()
    {
        static::creating(function ($penjualan) {
            $tanggal = today()->format('Ymd');

            // Hi-tung berapa penjualan y+ang sudah ada di tanggal ini
            $countToday = Penjualan::whereDate('created_at', today()->toDateString())->count() + 1;

            // Format dengan 3 digit (001, 002, dst)
            $urutan = str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $penjualan->kode_penjualan = 'LPEN'  . $tanggal  . $urutan;
        });
    }
}
