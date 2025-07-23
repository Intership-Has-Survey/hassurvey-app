<?php

namespace App\Filament\Resources\PemilikResource\Pages;

use App\Filament\Resources\PemilikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\RiwayatSewa;
use Illuminate\Support\Facades\DB;

class EditPemilik extends EditRecord
{
    protected static string $resource = PemilikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Ambil semua riwayat sewa yang terkait dengan pemilik ini
        $riwayatSewa = $this->record->riwayatSewaAlat;

        // 2. Hitung total dari kolom yang sudah ada
        $totalPendapatanKotor = $riwayatSewa->sum('biaya_sewa_alat');
        $totalPendapatanInvestor = $riwayatSewa->sum('pendapataninv');
        $totalPendapatanHasSurvey = $riwayatSewa->sum('pendapatanhas');

        // 3. Hitung Total Tagihan (yang belum lunas)
        $totalTagihan = $this->record->riwayatSewaAlat()
            ->whereHas('sewa.projects.statusPembayaran', function ($query) {
                $query->where('status', '!=', 'Lunas');
            })
            ->sum('biaya_sewa_alat');

        // 4. Masukkan hasil perhitungan ke dalam data form
        $data['total_pendapatanktr'] = 'Rp ' . number_format($totalPendapatanKotor, 0, ',', '.');
        $data['total_pendapataninv'] = 'Rp ' . number_format($totalPendapatanInvestor, 0, ',', '.');
        $data['total_pendapatanhas'] = 'Rp ' . number_format($totalPendapatanHasSurvey, 0, ',', '.');
        $data['total_tagihan'] = 'Rp ' . number_format($totalTagihan, 0, ',', '.');

        return $data;
    }
}
