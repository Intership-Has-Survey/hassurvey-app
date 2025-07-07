<?php

namespace App\Filament\Resources\StatusPekerjaanResource\Pages;

use App\Filament\Resources\StatusPekerjaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatusPekerjaan extends EditRecord
{
    protected static string $resource = StatusPekerjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
