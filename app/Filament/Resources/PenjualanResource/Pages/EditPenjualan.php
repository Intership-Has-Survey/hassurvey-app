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
            Actions\ViewAction::make(),
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->customerFlowType = $data['customer_flow_type'] ?? null;

        if ($this->customerFlowType === 'perorangan') {
            $data['corporate_id'] = null;
        }

        unset($data['customer_flow_type']);
        unset($data['perorangan_single']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->customerFlowType === 'corporate' && !empty($this->record->corporate_id) && !empty($this->record->perorangan_id)) {
            $corporate = $this->record->corporate;
            if ($corporate) {
                $corporate->perorangan()->syncWithoutDetaching([
                    $this->record->perorangan_id => ['user_id' => auth()->id()]
                ]);
            }
        }
    }
}
