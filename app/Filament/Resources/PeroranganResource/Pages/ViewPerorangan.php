<?php

namespace App\Filament\Resources\PeroranganResource\Pages;

use App\Filament\Resources\PeroranganResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerorangan extends ViewRecord
{
    protected static string $resource = PeroranganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
