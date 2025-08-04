<?php

namespace App\Filament\Resources\KewajibanBayarResource\Pages;

use App\Filament\Resources\KewajibanBayarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKewajibanBayar extends EditRecord
{
    protected static string $resource = KewajibanBayarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
