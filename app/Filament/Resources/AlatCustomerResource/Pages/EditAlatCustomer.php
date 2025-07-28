<?php

namespace App\Filament\Resources\AlatCustomerResource\Pages;

use App\Filament\Resources\AlatCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlatCustomer extends EditRecord
{
    protected static string $resource = AlatCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
