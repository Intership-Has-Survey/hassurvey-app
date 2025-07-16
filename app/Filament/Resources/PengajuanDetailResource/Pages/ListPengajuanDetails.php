<?php

namespace App\Filament\Resources\PengajuanDetailResource\Pages;

use App\Filament\Resources\PengajuanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanDetails extends ListRecords
{
    protected static string $resource = PengajuanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
