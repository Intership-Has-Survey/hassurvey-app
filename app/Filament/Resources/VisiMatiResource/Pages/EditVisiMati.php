<?php

namespace App\Filament\Resources\VisiMatiResource\Pages;

use App\Filament\Resources\VisiMatiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisiMati extends EditRecord
{
    protected static string $resource = VisiMatiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
