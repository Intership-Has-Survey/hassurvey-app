<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pemilik;
// use Barryvdh\DomPDF\PDF;
use Carbon\CarbonPeriod;
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    // public function preview($company, $investorId)
    // {
    //     // Gunakan investorId dari parameter route
    //     $id = $investorId;

    //     // Eager load relationships
    //     $investors = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->first();
    //     $record = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->find($id);

    //     if (!$record) {
    //         abort(404, 'Pemilik tidak ditemukan');
    //     }

    //     $items = $record->riwayatSewaAlat;

    //     // Eager load sampai corporate dengan relasi yang diperlukan untuk pembayaran
    //     $pemilik = Pemilik::with([
    //         'daftarAlat.sewa.corporate',
    //         'daftarAlat.sewa.perorangan',
    //         'daftarAlat.sewa'
    //     ])->findOrFail($id);

    //     // Tentukan periode
    //     $today = Carbon::now();
    //     $start_date = Carbon::create($today->year, $today->month, 28)->subMonth();
    //     $end_date = (clone $start_date)->addMonth()->subDay();

    //     $alatData = [];

    //     foreach ($pemilik->daftarAlat as $alat) {
    //         $period = CarbonPeriod::create($start_date, $end_date);
    //         $riwayat = [];

    //         foreach ($period as $tanggal) {
    //             $penyewaNama = null;
    //             $sewaData = null;
    //             $hargaFinal = 0;
    //             $sudahDibayar = 0;

    //             // Cari sewa yang aktif pada tanggal ini
    //             $activeSewa = $alat->sewa->first(function ($s) use ($tanggal, &$penyewaNama, &$sewaData, &$hargaFinal, &$sudahDibayar) {
    //                 $tglKeluar = $s->pivot->tgl_keluar;
    //                 $tglMasuk  = $s->pivot->tgl_masuk;

    //                 if (!$tglKeluar) {
    //                     return false;
    //                 }

    //                 $masihSewa = is_null($tglMasuk)
    //                     ? $tanggal->greaterThanOrEqualTo($tglKeluar)
    //                     : $tanggal->between($tglKeluar, $tglMasuk, true);

    //                 if ($masihSewa) {
    //                     $penyewaNama = optional($s->corporate)->nama ?? optional($s->perorangan->first())->nama ?? 'HAS Survey';
    //                     $sewaData = $s;
    //                     $hargaFinal = $s->pivot->harga_final ?? 0;
    //                     $sudahDibayar = $s->pivot->sudah_dibayar ?? 0;
    //                     return true;
    //                 }

    //                 return false;
    //             });

    //             $riwayat[] = [
    //                 'tanggal' => $tanggal->format('Y-m-d'),
    //                 'status' => $activeSewa ? 'ada sewa' : 'tidak ada sewa',
    //                 'status_invers' => $activeSewa ? 'kosong' : 'merah',
    //                 'penyewa' => $activeSewa ? $penyewaNama : '',
    //                 'carbon_tanggal' => $tanggal,
    //                 'sewa_data' => $activeSewa ? $sewaData : null,
    //                 'harga_final' => $activeSewa ? $hargaFinal : 0,
    //                 'sudah_dibayar' => $activeSewa ? $sudahDibayar : 0,
    //             ];
    //         }

    //         // Grouping berdasarkan periode sewa yang berurutan
    //         $groupedRiwayat = [];
    //         $currentGroup = [];
    //         $previousPenyewa = null;

    //         foreach ($riwayat as $index => $day) {
    //             // Jika penyewa berbeda atau status berbeda, buat group baru
    //             if ($day['penyewa'] !== $previousPenyewa || empty($currentGroup)) {
    //                 if (!empty($currentGroup)) {
    //                     $groupedRiwayat[] = $currentGroup;
    //                 }
    //                 $currentGroup = [
    //                     'start_date' => $day['carbon_tanggal'],
    //                     'end_date' => $day['carbon_tanggal'],
    //                     'penyewa' => $day['penyewa'],
    //                     'status' => $day['status'],
    //                     'days' => [$day],
    //                     'sewa_data' => $day['sewa_data'],
    //                     'harga_final' => $day['harga_final'],
    //                     'sudah_dibayar' => $day['sudah_dibayar'],
    //                 ];
    //             } else {
    //                 // Masukkan ke group yang sama
    //                 $currentGroup['end_date'] = $day['carbon_tanggal'];
    //                 $currentGroup['days'][] = $day;
    //             }

    //             $previousPenyewa = $day['penyewa'];

    //             // Untuk hari terakhir
    //             if ($index === count($riwayat) - 1 && !empty($currentGroup)) {
    //                 $groupedRiwayat[] = $currentGroup;
    //             }
    //         }

    //         $alatData[] = [
    //             'alat' => $alat,
    //             'riwayat' => $riwayat,
    //             'grouped_riwayat' => $groupedRiwayat,
    //         ];
    //     }

    //     return view('exports.investor_update', compact([
    //         'investors',
    //         'record',
    //         'items',
    //         'alatData',
    //         'pemilik',
    //         'start_date',
    //         'end_date'
    //     ]));
    // }

    public function preview($company, $investorId)
    {
        // Gunakan investorId dari parameter route
        $id = $investorId;

        // Eager load relationships
        $investors = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->first();
        $record = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->find($id);

        if (!$record) {
            abort(404, 'Pemilik tidak ditemukan');
        }

        // Tentukan periode
        $today = Carbon::now();
        $start_date = Carbon::create($today->year, $today->month, 28)->subMonth();
        $end_date = (clone $start_date)->addMonth()->subDay();

        // Filter items berdasarkan periode
        $items = $record->riwayatSewaAlat->filter(function ($item) use ($start_date, $end_date) {
            $tglKeluar = Carbon::parse($item->tgl_keluar);

            // Cek apakah tgl_keluar berada dalam periode
            return $tglKeluar->between($start_date, $end_date);
        })->sortBy('tgl_keluar'); // Urutkan berdasarkan tanggal

        // Eager load sampai corporate dengan relasi yang diperlukan untuk pembayaran
        $pemilik = Pemilik::with([
            'daftarAlat.sewa.corporate',
            'daftarAlat.sewa.perorangan',
            'daftarAlat.sewa'
        ])->findOrFail($id);

        $alatData = [];

        foreach ($pemilik->daftarAlat as $alat) {
            $period = CarbonPeriod::create($start_date, $end_date);
            $riwayat = [];

            foreach ($period as $tanggal) {
                $penyewaNama = null;
                $sewaData = null;
                $hargaFinal = 0;
                $sudahDibayar = 0;

                // Cari sewa yang aktif pada tanggal ini
                $activeSewa = $alat->sewa->first(function ($s) use ($tanggal, &$penyewaNama, &$sewaData, &$hargaFinal, &$sudahDibayar) {
                    $tglKeluar = $s->pivot->tgl_keluar;
                    $tglMasuk  = $s->pivot->tgl_masuk;

                    if (!$tglKeluar) {
                        return false;
                    }

                    $masihSewa = is_null($tglMasuk)
                        ? $tanggal->greaterThanOrEqualTo($tglKeluar)
                        : $tanggal->between($tglKeluar, $tglMasuk, true);

                    if ($masihSewa) {
                        $penyewaNama = optional($s->corporate)->nama ?? optional($s->perorangan->first())->nama ?? 'HAS Survey';
                        $sewaData = $s;
                        $hargaFinal = $s->pivot->harga_final ?? 0;
                        $sudahDibayar = $s->pivot->sudah_dibayar ?? 0;
                        return true;
                    }

                    return false;
                });

                $riwayat[] = [
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'status' => $activeSewa ? 'ada sewa' : 'tidak ada sewa',
                    'status_invers' => $activeSewa ? 'kosong' : 'merah',
                    'penyewa' => $activeSewa ? $penyewaNama : '',
                    'carbon_tanggal' => $tanggal,
                    'sewa_data' => $activeSewa ? $sewaData : null,
                    'harga_final' => $activeSewa ? $hargaFinal : 0,
                    'sudah_dibayar' => $activeSewa ? $sudahDibayar : 0,
                ];
            }

            // Grouping berdasarkan periode sewa yang berurutan
            $groupedRiwayat = [];
            $currentGroup = [];
            $previousPenyewa = null;

            foreach ($riwayat as $index => $day) {
                // Jika penyewa berbeda atau status berbeda, buat group baru
                if ($day['penyewa'] !== $previousPenyewa || empty($currentGroup)) {
                    if (!empty($currentGroup)) {
                        $groupedRiwayat[] = $currentGroup;
                    }
                    $currentGroup = [
                        'start_date' => $day['carbon_tanggal'],
                        'end_date' => $day['carbon_tanggal'],
                        'penyewa' => $day['penyewa'],
                        'status' => $day['status'],
                        'days' => [$day],
                        'sewa_data' => $day['sewa_data'],
                        'harga_final' => $day['harga_final'],
                        'sudah_dibayar' => $day['sudah_dibayar'],
                    ];
                } else {
                    // Masukkan ke group yang sama
                    $currentGroup['end_date'] = $day['carbon_tanggal'];
                    $currentGroup['days'][] = $day;
                }

                $previousPenyewa = $day['penyewa'];

                // Untuk hari terakhir
                if ($index === count($riwayat) - 1 && !empty($currentGroup)) {
                    $groupedRiwayat[] = $currentGroup;
                }
            }

            $alatData[] = [
                'alat' => $alat,
                'riwayat' => $riwayat,
                'grouped_riwayat' => $groupedRiwayat,
            ];
        }

        return view('exports.investor_update', compact([
            'investors',
            'record',
            'items',
            'alatData',
            'pemilik',
            'start_date',
            'end_date'
        ]));
    }

    // Optional: Method untuk generate PDF file langsung
    public function download($company, $investorId)
    {
        $data = $this->preview($company, $investorId)->getData();

        $pdf = PDF::loadView('exports.investor_update', $data);

        return $pdf->download("investor-update-{$investorId}.pdf");
    }
}
