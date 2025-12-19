<?php

namespace App\Models;

use App\Models\AlatSewa;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function personel()
    {
        return $this->belongsToMany(Personel::class, 'personel_project')
            ->using(PersonelProject::class)
            ->withPivot('id', 'user_id', 'peran', 'tanggal_mulai', 'tanggal_berakhir')
            ->withTimestamps();
    }

    public function personels()
    {
        return $this->hasMany(PersonelProject::class);
        // ->using(PersonelProject::class)
        // ->withPivot('id', 'user_id', 'peran', 'tanggal_mulai', 'tanggal_berakhir')
        // ->withTimestamps();
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function perorangan(): BelongsToMany
    {
        return $this->belongsToMany(Perorangan::class, 'project_perorangan')
            ->withPivot('perorangan_id', 'project_id', 'peran')
            ->withTimestamps();
    }

    public function statusPekerjaan()
    {
        return $this->hasMany(StatusPekerjaan::class);
    }

    public function statusPembayaran()
    {
        return $this->morphMany(StatusPembayaran::class, 'payable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function pengajuanDanas(): HasMany
    // {
    //     return $this->hasMany(PengajuanDana::class);
    // }

    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'riwayat_sewa', 'project_id', 'daftar_alat_id')
            ->using(AlatSewa::class)
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa_alat', 'user_id'])
            ->withTimestamps();
    }

    public function sewa()
    {
        return $this->belongsTo(Sewa::class, 'sewa_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pembayaranPersonel()
    {
        return $this->hasMany(PembayaranPersonel::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_project', 'kategori_id', 'sales_id', 'tanggal_informasi_masuk', 'sumber', 'provinsi', 'kota', 'kecamatan', 'desa', 'detail_alamat', 'nilai_project_awal', 'dikenakan_ppn', 'nilai_ppn', 'nilai_project', 'status', 'status_pembayaran', 'status_pekerjaan', 'corporate_id', 'sewa_id'])
            ->logOnlyDirty()
            ->useLogName('Project');
    }

    protected static function booted()
    {
        static::creating(function ($project) {
            $tanggal = today()->format('Ymd');

            // Hi-tung berapa project yang sudah ada di tanggal ini
            $countToday = Project::whereDate('created_at', today()->toDateString())->count() + 1;

            // Format dengan 3 digit (001, 002, dst)
            $urutan = str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $project->kode_project = 'LPEM' . $tanggal .  $urutan;
        });
    }

    public function pengajuanDanas()
    {
        return $this->morphMany(PengajuanDana::class, 'pengajuanable');
    }

    public function provinsiRegion(): BelongsTo
    {
        return $this->belongsTo(TrefRegion::class, 'provinsi', 'code');
    }

    // Relationship for kota
    public function kotaRegion(): BelongsTo
    {
        return $this->belongsTo(TrefRegion::class, 'kota', 'code');
    }

    // Relationship for kecamatan
    public function kecamatanRegion(): BelongsTo
    {
        return $this->belongsTo(TrefRegion::class, 'kecamatan', 'code');
    }

    // Relationship for desa
    public function desaRegion(): BelongsTo
    {
        return $this->belongsTo(TrefRegion::class, 'desa', 'code');
    }

    public function picInternal(): BelongsTo
    {
        return $this->belongsTo(PicInternal::class, 'pic_internal_id');
    }

    // public function invoices()
    // {
    //     return $this->hasMany(Invoice::class, 'customer');
    // }

    // public function invoices()
    // {
    //     return $this->morphMany(Invoice::class, 'invoiceable');
    // }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable', 'customer_type', 'customer_id');
    }


    public function acara()
    {
        return $this->hasOne(Acara::class);
    }
}
