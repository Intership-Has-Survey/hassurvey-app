<?php

namespace App\Filament\Resources\DaftarAlatResource\Pages;

use App\Filament\Resources\DaftarAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDaftarAlat extends ViewRecord
{
    protected static string $resource = DaftarAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
