<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKalibrasis extends ListRecords
{
    protected static string $resource = KalibrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
