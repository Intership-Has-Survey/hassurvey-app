<?php

namespace App\Filament\Resources\DaftarAlatResource\Pages;

use App\Filament\Resources\DaftarAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarAlat extends EditRecord
{
    protected static string $resource = DaftarAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
