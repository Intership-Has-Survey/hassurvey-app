<?php

namespace App\Filament\Resources\PeralatanKerjaResource\Pages;

use App\Filament\Resources\PeralatanKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeralatanKerjas extends ListRecords
{
    protected static string $resource = PeralatanKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
