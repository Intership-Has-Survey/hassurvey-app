<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Corporate;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    public ?string $customerFlowType = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->customerFlowType = $data['customer_flow_type'] ?? null;

        if ($this->customerFlowType === 'perorangan') {
            $data['corporate_id'] = null;
        }

        unset($data['customer_flow_type']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->customerFlowType === 'corporate' && !empty($this->record->corporate_id)) {
            $corporate = $this->record->corporate;
            if ($corporate) {
                $peroranganIds = $this->record->perorangan()->pluck('id')->toArray();
                foreach ($peroranganIds as $peroranganId) {
                    // Attach perorangan to corporate if not already attached
                    if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
                        $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                    }
                }
            }
        }
    }
}
