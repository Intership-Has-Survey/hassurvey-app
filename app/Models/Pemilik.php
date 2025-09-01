<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Pemilik extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'pemilik';

    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->hasMany(DaftarAlat::class, 'pemilik_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($pemilik) {
            if (!$pemilik->user_id && Auth::check()) {
                $pemilik->user_id = Auth::id();
            }
        });
    }

    public function riwayatSewaAlat(): HasManyThrough
    {
        /**
         * Method ini mengambil RiwayatSewa MELALUI DaftarAlat.
         * Laravel secara otomatis akan menghubungkan:
         * pemilik.id -> daftar_alat.pemilik_id
         * daftar_alat.id -> riwayat_sewa.daftar_alat_id
         */
        return $this->hasManyThrough(AlatSewa::class, DaftarAlat::class);
    }

    public function statusPengeluarans()
    {
        return $this->morphMany(TransaksiPembayaran::class, 'payable');
    }

    public function getStatusPembayaranBulanIniAttribute()
    {
        $now = Carbon::now();

        // Determine the current period: from 27th of previous month to 26th of current month
        $periodStart = $now->copy()->subMonth()->setDay(27);
        $periodEnd = $now->copy()->setDay(26);

        // Find payment that covers the current period
        $payment = $this->statusPengeluarans()
            ->whereBetween('tanggal_transaksi', [$periodStart->startOfDay(), $periodEnd->endOfDay()])
            ->orderBy('tanggal_transaksi', 'desc')
            ->first();

        if ($payment) {
            return 'Lunas';
        }

        return 'Belum Dibayar';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Pemilik');
    }
}
