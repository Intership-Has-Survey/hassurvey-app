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

        $items = $record->riwayatSewaAlat;

        // Eager load sampai corporate
        $pemilik = Pemilik::with('daftarAlat.sewa.corporate', 'daftarAlat.sewa.perorangan')->findOrFail($id);

        // Tentukan periode
        $today = Carbon::now();
        $start_date = Carbon::create($today->year, $today->month, 28)->subMonth();
        $end_date = (clone $start_date)->addMonth()->subDay();

        $alatData = [];

        foreach ($pemilik->daftarAlat as $alat) {
            $period = CarbonPeriod::create($start_date, $end_date);
            $riwayat = [];

            foreach ($period as $tanggal) {
                $penyewaNama = null;

                $adaSewa = $alat->sewa->contains(function ($s) use ($tanggal, &$penyewaNama) {
                    $tglKeluar = $s->pivot->tgl_keluar;
                    $tglMasuk  = $s->pivot->tgl_masuk;

                    if (!$tglKeluar) {
                        return false;
                    }

                    // Jika tgl_masuk null → masih disewa
                    $masihSewa = is_null($tglMasuk)
                        ? $tanggal->greaterThanOrEqualTo($tglKeluar)
                        : $tanggal->between($tglKeluar, $tglMasuk, true);

                    if ($masihSewa) {
                        $penyewaNama = optional($s->corporate)->nama ?? optional($s->perorangan->first())->nama ?? 'HAS Survey';
                    }

                    return $masihSewa;
                });

                $riwayat[] = [
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'status' => $adaSewa ? 'ada sewa' : 'tidak ada sewa',
                    'status_invers' => $adaSewa ? 'kosong' : 'merah',
                    'penyewa' => $adaSewa ? $penyewaNama : '',
                ];
            }

            $alatData[] = [
                'alat' => $alat,
                'riwayat' => $riwayat,
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
