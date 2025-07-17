<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function personels()
    {
        return $this->belongsToMany(Personel::class, 'personel_project')
            ->withPivot('user_id', 'peran', 'tanggal_mulai', 'tanggal_berakhir')
            ->withTimestamps();
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function perorangan(): BelongsTo
    {
        return $this->belongsTo(Perorangan::class);
    }

    public function statusPekerjaan()
    {
        return $this->hasMany(StatusPekerjaan::class);
    }

    // public function StatusPembayaran()
    // {
    //     return $this->hasMany(StatusPembayaran::class);
    // }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->morphTo();
    }

    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class);
    }
    public function Sewa()
    {
        return $this->belongsTo(Sewa::class);
    }

    public function customers()
    {
        return $this->morphTo();
    }


    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'riwayat_sewa', 'sewa_id', 'daftar_alat_id')
            ->using(RiwayatSewa::class)
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa_alat', 'user_id'])
            ->withTimestamps();
    }
}
