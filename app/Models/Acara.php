<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Acara extends Model
{
    //
    use HasUuids;
    protected $table = 'acaras';
    protected $guarded = ['id'];


    public function project()
    {
        return $this->belongsTo(Project::class);
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

    protected static function booted()
    {
        static::creating(function ($acara) {

            // 🔒 Cegah 1 project punya >1 acara
            if (static::where('project_id', $acara->project_id)->exists()) {
                return false;
            }

            // 🏢 Ambil company_id dari project
            if (! $acara->company_id) {
                $acara->company_id = $acara->project->company_id;
            }

            // 🔢 Generate nomor otomatis
            if (! $acara->nomor) {

                $tahun = now()->year;
                $bulan = now()->month;

                // Konversi bulan ke Romawi
                $bulanRomawi = [
                    1  => 'I',
                    2  => 'II',
                    3  => 'III',
                    4  => 'IV',
                    5  => 'V',
                    6  => 'VI',
                    7  => 'VII',
                    8  => 'VIII',
                    9  => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII',
                ][$bulan];

                // Urutan per company per bulan per tahun
                $urutan = static::where('company_id', $acara->company_id)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->count() + 1;

                // Format 001/BAST/PEMETAAN/X/2025
                $acara->nomor = sprintf(
                    '%03d/BAST/PEMETAAN/%s/%d',
                    $urutan,
                    $bulanRomawi,
                    $tahun
                );
            }
        });
    }
}
