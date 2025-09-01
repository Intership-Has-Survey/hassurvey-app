<?php

namespace App\Filament\Resources\PersonelResource\Pages;

use App\Filament\Resources\PersonelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonel extends ViewRecord
{
    protected static string $resource = PersonelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
