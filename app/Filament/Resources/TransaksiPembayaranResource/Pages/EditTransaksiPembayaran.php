<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Resources\TransaksiPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPembayaran extends EditRecord
{
    protected static string $resource = TransaksiPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
