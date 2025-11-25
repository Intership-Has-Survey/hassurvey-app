<?php

namespace App\Filament\Resources\KategoriPengajuanResource\Pages;

use App\Filament\Resources\KategoriPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPengajuan extends EditRecord
{
    protected static string $resource = KategoriPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
