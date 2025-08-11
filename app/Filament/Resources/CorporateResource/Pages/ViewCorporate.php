<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\CorporateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCorporate extends ViewRecord
{
    protected static string $resource = CorporateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
