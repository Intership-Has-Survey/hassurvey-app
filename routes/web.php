<?php

use Carbon\Carbon;
use App\Models\Pemilik;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Http\Controllers\Controller;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AcaraController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect('/admin');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::get('/admin/{uuid}', [Controller::class, 'setCompanyContext']);

Route::get('/admin/e703370f-5ac6-4c4f-9b04-3a360bd529f7/investor/preview', function () {
    // Hilal
    $id = "01997447-68ba-71b5-9a4b-a2075954fe3d";
    $company = "e703370f-5ac6-4c4f-9b04-3a360bd529f7";
    //with harus dibaris pertama, karena first atau get mengembalika collection bukan query
    // $investors = Pemilik::first()->with(['riwayatSewaAlat.sewa.corporate']);
    $investors = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->first();
    $record = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->find($id);
    $items = $record->riwayatSewaAlat;
    //karena HASManny maka tidak perlu tanda kurung
    // $items = Pemilik::find($id)->riwayatSewaAlat->with(['sewa.corporate']);

    //COmpact untuk mengirim data ke blade
    return view('exports.investor', compact(['investors', 'record', 'items']));
});

Route::get('/admin/e703370f-5ac6-4c4f-9b04-3a360bd529f7/investor/pdfpreview', function () {
    $id = "01997447-68ba-71b5-9a4b-a2075954fe3d";
    $record = Pemilik::with(['riwayatSewaAlat.daftarAlat.merk', 'riwayatSewaAlat.daftarAlat.jenisAlat', 'daftarAlat.merk', 'daftarAlat.jenisAlat'])
        ->find($id);


    $items = $record->riwayatSewaAlat;
    $pdf = Pdf::loadView('exports.investor', compact('record', 'items'))
        ->setPaper('a4', 'portrait');
    return view('exports.investorpdf', compact(['record', 'items']));
});


Route::get('/admin/e703370f-5ac6-4c4f-9b04-3a360bd529f7/investos/preview', function () {
    $id = "01997447-68ba-71b5-9a4b-a2075954fe3d";

    // Eager load sampai corporate
    $pemilik = Pemilik::with('daftarAlat.sewa.corporate', 'daftarAlat.sewa.perorangan')->findOrFail($id);
    // @dd($pemilik->daftarAlat::first()->sewa->corporate);
    // @dd($pemilik->perorangan);
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

                // @dd($s->perorangan);
                if ($masihSewa) {
                    // @dd($s->perorangan());
                    $penyewaNama = optional($s->corporate)->nama ?? optional($s->perorangan->first())->nama ?? 'HAS Survey';
                }

                return $masihSewa;
            });

            $riwayat[] = [
                'tanggal' => $tanggal->format('Y-m-d'),
                'status' => $adaSewa ? 'ada sewa' : 'tidak ada sewa',
                'status_invers' => $adaSewa ? 'kosong' : 'merah',
                'penyewa' => $adaSewa ? $penyewaNama : '-',
            ];
        }

        $alatData[] = [
            'alat' => $alat,
            'riwayat' => $riwayat,
        ];
    }

    return view('exports.investors', compact('pemilik', 'alatData', 'start_date', 'end_date'));
});

Route::get('/admin/e703370f-5ac6-4c4f-9b04-3a360bd529f7/investorupdate/preview', function () {
    // Hilal
    $id = "01997447-68ba-71b5-9a4b-a2075954fe3d";
    $company = "e703370f-5ac6-4c4f-9b04-3a360bd529f7";
    //with harus dibaris pertama, karena first atau get mengembalika collection bukan query
    // $investors = Pemilik::first()->with(['riwayatSewaAlat.sewa.corporate']);
    $investors = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->first();
    $record = Pemilik::with(['riwayatSewaAlat.sewa.corporate'])->find($id);
    $items = $record->riwayatSewaAlat;

    $id = "01997447-68ba-71b5-9a4b-a2075954fe3d";

    // Eager load sampai corporate
    $pemilik = Pemilik::with('daftarAlat.sewa.corporate', 'daftarAlat.sewa.perorangan')->findOrFail($id);
    // @dd($pemilik->daftarAlat::first()->sewa->corporate);
    // @dd($pemilik->perorangan);
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

                // @dd($s->perorangan);
                if ($masihSewa) {
                    // @dd($s->perorangan());
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

    // return view('exports.investors', compact('pemilik', 'alatData', 'start_date', 'end_date'));
    //karena HASManny maka tidak perlu tanda kurung
    // $items = Pemilik::find($id)->riwayatSewaAlat->with(['sewa.corporate']);

    //COmpact untuk mengirim data ke blade
    return view('exports.investor_update', compact(['investors', 'record', 'items', 'alatData', 'pemilik', 'start_date', 'end_date']));
});

// Route::get('/admin/{company}/investorupdate/preview/{investor}', [PdfController::class, 'preview'])
//     ->name('pdf.preview');

Route::get('/admin/{company}/investorupdate/download/{investor}', [PdfController::class, 'download'])
    ->name('pdf.download');

Route::get('/admin/{company}/investorupdate/preview/{investor}', [PdfController::class, 'preview'])
    ->name('pdf.preview');

Route::get('/admin/{company}/investorupdate/select/{investor}', [PdfController::class, 'selectPeriod'])
    ->name('pdf.select');
Route::get('/admin/{company}/berita-acara/{project}', [AcaraController::class, 'preview'])
    ->name('acara');
Route::get('/admin/invoice', [InvoiceController::class, 'preview'])
    ->name('invoicepreview');
Route::get('/admin/{company}/invoice/{invoice}', [InvoiceController::class, 'preview'])
    ->name('invoice');
require __DIR__ . '/auth.php';
