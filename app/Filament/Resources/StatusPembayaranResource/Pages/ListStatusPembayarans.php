<?php

namespace App\Filament\Resources\StatusPembayaranResource\Pages;

use App\Filament\Resources\StatusPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatusPembayarans extends ListRecords
{
    protected static string $resource = StatusPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
