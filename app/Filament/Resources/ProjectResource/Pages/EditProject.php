<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\RingkasanPemetaan;
use App\Models\Corporate;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

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

        return $data;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RingkasanPemetaan::class,
        ];
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
