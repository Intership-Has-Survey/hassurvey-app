<?php

namespace App\Filament\Resources\AlatCustomerResource\Pages;

use App\Filament\Resources\AlatCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlatCustomer extends ViewRecord
{
    protected static string $resource = AlatCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }
}
