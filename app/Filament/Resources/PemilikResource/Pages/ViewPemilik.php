<?php

namespace App\Filament\Resources\PemilikResource\Pages;

use App\Filament\Resources\PemilikResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPemilik extends ViewRecord
{
    protected static string $resource = PemilikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
