<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Resources\TransaksiPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaksiPembayaran extends ViewRecord
{
    protected static string $resource = TransaksiPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
