<?php

namespace App\Filament\Resources\DataPembayaranResource\Pages;

use App\Filament\Resources\DataPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataPembayarans extends ListRecords
{
    protected static string $resource = DataPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
