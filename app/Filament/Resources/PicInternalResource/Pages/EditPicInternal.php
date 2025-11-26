<?php

namespace App\Filament\Resources\PicInternalResource\Pages;

use App\Filament\Resources\PicInternalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPicInternal extends EditRecord
{
    protected static string $resource = PicInternalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
