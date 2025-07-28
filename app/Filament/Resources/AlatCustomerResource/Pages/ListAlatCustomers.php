<?php

namespace App\Filament\Resources\AlatCustomerResource\Pages;

use App\Filament\Resources\AlatCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlatCustomers extends ListRecords
{
    protected static string $resource = AlatCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
