<?php

namespace App\Filament\Resources\DetailPengajuanResource\Pages;

use App\Filament\Resources\DetailPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailPengajuans extends ListRecords
{
    protected static string $resource = DetailPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
