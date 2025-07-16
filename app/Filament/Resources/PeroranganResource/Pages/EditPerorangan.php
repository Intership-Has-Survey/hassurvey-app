<?php

namespace App\Filament\Resources\PeroranganResource\Pages;

use App\Filament\Resources\PeroranganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerorangan extends EditRecord
{
    protected static string $resource = PeroranganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
