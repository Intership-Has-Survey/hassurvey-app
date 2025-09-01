<?php

namespace App\Filament\Resources\StatusPembayaranResource\Pages;

use App\Filament\Resources\StatusPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStatusPembayaran extends ViewRecord
{
    protected static string $resource = StatusPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
