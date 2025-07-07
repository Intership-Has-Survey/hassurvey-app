<?php

namespace App\Filament\Resources\StatusPembayaranResource\Pages;

use App\Filament\Resources\StatusPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatusPembayaran extends EditRecord
{
    protected static string $resource = StatusPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
