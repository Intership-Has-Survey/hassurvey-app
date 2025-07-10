<?php

namespace App\Filament\Resources\JenisPekerjaanResource\Pages;

use App\Filament\Resources\JenisPekerjaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisPekerjaan extends EditRecord
{
    protected static string $resource = JenisPekerjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
