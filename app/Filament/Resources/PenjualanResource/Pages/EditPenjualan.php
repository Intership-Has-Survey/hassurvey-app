<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

    public ?string $customerFlowType = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Logika Tipe Customer
        $data['customer_flow_type'] = filled($data['corporate_id']) ? 'corporate' : 'perorangan';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->customerFlowType = $data['customer_flow_type'] ?? null;
        if ($this->customerFlowType === 'perorangan') {
            $data['corporate_id'] = null;
        }
        unset($data['customer_flow_type']);

        return $data;
    }
}
