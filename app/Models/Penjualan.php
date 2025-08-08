<?php

namespace App\Models;

use App\Models\DetailPenjualan;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'penjualans';

    protected $guarded = [];

    protected $appends = ['total_items'];

    public function getTotalItemsAttribute(): string
    {
        return 'Rp ' . number_format($this->detailPenjualan->sum('harga'), 0, ',', '.');
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

    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
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
            ->logOnly(['nama_project', 'kategori_id', 'sales_id', 'tanggal_informasi_masuk', 'sumber', 'provinsi', 'kota', 'kecamatan', 'desa', 'detail_alamat', 'nilai_project_awal', 'dikenakan_ppn', 'nilai_ppn', 'nilai_project', 'status', 'status_pembayaran', 'status_pekerjaan', 'corporate_id', 'sewa_id'])
            ->logOnlyDirty()
            ->useLogName('Project');
    }
}
