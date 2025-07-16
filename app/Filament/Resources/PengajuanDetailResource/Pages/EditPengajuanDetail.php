<?php

namespace App\Filament\Resources\PengajuanDetailResource\Pages;

use App\Filament\Resources\PengajuanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanDetail extends EditRecord
{
    protected static string $resource = PengajuanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
