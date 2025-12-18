<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

class Penawaran extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Corporate::class, 'customer_id');
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
    public static function getPrefixFromModel()
    {
        $company = Filament::getTenant();

        if (! $company || ! $company->name) {
            return 'QTN';
        }

        $name = strtoupper(trim($company->name));

        if (Str::startsWith($name, 'PT')) {
            return 'HSGI-QTN';
        }

        if (Str::startsWith($name, 'CV')) {
            return 'CVHS-QTN';
        }

        return 'QTN'; // fallback jika format aneh
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

    public static function generateKodePenawaranFromModel()
    {
        $prefix = self::getPrefixFromModel();

        $tahun = date('Y');
        //leading 0, ex: 01,02,dll
        $bulan = date('m');
        //non-leading zero ex: 1,2,3, 
        // $bulan = date('n');
        $bulanRomawi = self::bulanRomawi($bulan);
        $companyId = Filament::getTenant()->getKey();

        // Cari invoice sebelumnya berdasarkan bulan + tahun + tipe invoiceable
        $lastpenawaran = self::where('company_id', $companyId)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastpenawaran) {
            $parts = explode('/', $lastpenawaran->kode_penawaran);
            $lastNumber = intval($parts[3] ?? 0);
            $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next = '001';
        }

        return "{$prefix}/{$tahun}/{$bulanRomawi}/{$next}";
    }

    //END GENERATE CODE

    protected static function booted()
    {
        static::creating(function ($project) {
            // $tanggal = today()->format('Ymd');

            // Hi-tung berapa project yang sudah ada di tanggal ini
            // $countToday = Project::whereDate('created_at', today()->toDateString())->count() + 1;

            // Format dengan 3 digit (001, 002, dst)
            // $urutan = str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // $project->kode_project = 'LPEM' . $tanggal .  $urutan;
        });
    }
}
