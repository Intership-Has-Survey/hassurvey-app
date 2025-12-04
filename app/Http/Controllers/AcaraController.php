<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Berita;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AcaraController extends Controller
{

    private function toRoman($month)
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
            12 => 'XII'
        ];

        return $romawi[$month];
    }

    private function generateNomor($company)
    {
        $now = Carbon::now();
        $bulan = $now->month;
        $tahun = $now->year;

        // Ambil nomor terakhir di bulan & tahun yang sama
        $last = Berita::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('company_id', $company)
            ->orderBy('urut', 'desc')
            ->first();

        // Tentukan urut
        $urut = $last ? $last->urut + 1 : 1;

        // Format 3 digit
        $urutFormatted = str_pad($urut, 3, '0', STR_PAD_LEFT);

        // Bulan romawi
        $bulanR = $this->toRoman($bulan);

        // Bentuk nomor lengkap
        $nomor = "{$urutFormatted}/BAST/PEMETAAN/{$bulanR}/{$tahun}";

        // Simpan ke database
        Berita::create([
            'nomor' => $nomor,
            'urut' => $urut,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'company_id' => $company
        ]);

        return $nomor;
    }

    // public function preview($company, $projectId,)
    // {
    //     // Validasi input
    //     $project = Project::where('id', $projectId)->firstOrFail();
    //     return view('exports.acara', compact('project'));
    // }

    public function preview($company, $projectId)
    {
        $project = Project::findOrFail($projectId);

        // Panggil generator nomor
        $nomor = $this->generateNomor($company);

        return view('exports.acara', compact('project', 'nomor'));
    }
}
