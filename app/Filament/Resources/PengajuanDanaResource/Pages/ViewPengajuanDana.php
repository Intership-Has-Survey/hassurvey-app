<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use App\Filament\Resources\PengajuanDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuanDana extends ViewRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
