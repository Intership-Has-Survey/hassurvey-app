<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Pages\RingkasanTransaksi;
use App\Filament\Resources\TransaksiPembayaranResource;
use App\Models\PengajuanDana;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPembayarans extends ListRecords
{
    protected static string $resource = TransaksiPembayaranResource::class;
    protected static ?string $title = 'Pengeluaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
