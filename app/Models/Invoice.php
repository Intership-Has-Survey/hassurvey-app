<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Iluminate\Database\Eloquent\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends Model
{
    use HasUuids;
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan(): BelongsToMany
    {
        return $this->belongsToMany(Perorangan::class)
            ->withPivot('perorangan_id', 'sewa_id', 'peran')
            ->withTimestamps();
    }

    public function detailInvoices(): HasMany
    {
        return $this->hasMany(DetailInvoice::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'customer');
    }

    public function getTotalHargaAttribute(): string
    {
        return $this->detailInvoices->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
    }

    // public function invoiceable()
    // {
    //     return $this->morphTo();
    // }

    public function invoiceable()
    {
        return $this->morphTo(null, 'customer_type', 'customer_id');
    }

    public static function getPrefixFromModel($model)
    {
        return match (get_class($model)) {
            \App\Models\Project::class => 'LPEM',
            \App\Models\Sewa::class    => 'LSEW',
            \App\Models\Kalibrasi::class => 'LKAL',
            \App\Models\Penjualan::class => 'LPEN',
            default => '-',
        };
    }

    public static function bulanRomawi($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];
        return $romawi[$bulan];
    }



    public static function generateKodeInvoiceFromModel($invoiceable)
    {
        $prefix = self::getPrefixFromModel($invoiceable);

        $tahun = date('Y');
        $bulan = date('n');
        $bulanRomawi = self::bulanRomawi($bulan);

        // Cari invoice sebelumnya berdasarkan bulan + tahun + tipe invoiceable
        $lastInvoice = self::where('customer_type', get_class($invoiceable))
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->kode_invoice);
            $lastNumber = intval($parts[4] ?? 0);
            $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next = '001';
        }

        return "INV/{$prefix}/{$tahun}/{$bulanRomawi}/{$next}";
    }
}
