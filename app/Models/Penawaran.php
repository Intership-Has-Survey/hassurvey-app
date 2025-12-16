<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penawaran extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corporate_id');
    }

    public function perorangan()
    {
        return $this->belongsToMany(Perorangan::class)
            ->withPivot('perorangan_id', 'sewa_id', 'peran')
            ->withTimestamps();
    }

    public function detailPenawarans()
    {
        return $this->hasMany(DetailPenawaran::class);
    }

    public function getTotalHargaAttribute(): string
    {
        return $this->detailPenawarans->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
    }

    public function penawaranable()
    {
        return $this->morphTo(null, 'customer_type', 'customer_id');
    }

    //start generate code
    public static function getPrefixFromModel($model)
    {
        return 'HSGI-QTN';
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

    public static function generateKodePenawaranFromModel($penawaranable)
    {
        $prefix = self::getPrefixFromModel($penawaranable);

        $tahun = date('Y');
        $bulan = date('n');
        $bulanRomawi = self::bulanRomawi($bulan);

        // Cari invoice sebelumnya berdasarkan bulan + tahun + tipe invoiceable
        $lastInvoice = self::where('customer_type', get_class($penawaranable))
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

    //END GENERATE CODE
}
