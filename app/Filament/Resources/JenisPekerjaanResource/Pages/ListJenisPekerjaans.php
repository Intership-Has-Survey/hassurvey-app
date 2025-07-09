<?php

namespace App\Filament\Resources\JenisPekerjaanResource\Pages;

use App\Filament\Resources\JenisPekerjaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisPekerjaans extends ListRecords
{
    protected static string $resource = JenisPekerjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
