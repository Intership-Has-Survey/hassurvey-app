<?php

namespace App\Filament\Resources\AlatCustomerResource\Pages;

use App\Filament\Resources\AlatCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAlatCustomer extends CreateRecord
{
    protected static string $resource = AlatCustomerResource::class;

    public ?string $customerFlowType = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->customerFlowType = $data['customer_flow_type'] ?? null;

        if ($this->customerFlowType === 'perorangan') {
            $data['corporate_id'] = null;
        }

        unset($data['customer_flow_type']);

        return $data;
    }

    // protected function afterCreate(): void
    // {
    //     if ($this->customerFlowType === 'corporate' && !empty($this->record->corporate_id)) {
    //         $corporate = $this->record->corporate;
    //         if ($corporate) {
    //             $peroranganIds = $this->record->perorangan()->pluck('id')->toArray();
    //             foreach ($peroranganIds as $peroranganId) {
    //                 if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
    //                     $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
    //                 }
    //             }
    //         }
    //     }
    // }

    protected function afterCreate(): void
    {
        // Simpan relasi many-to-many dengan peran
        $record = $this->getRecord();
        $data = $this->data;

        if (isset($data['customer_flow_type']) && $data['customer_flow_type'] === 'perorangan' && isset($data['perorangan_ids'])) {
            $peran = $record->corporate_id ? $record->corporate->nama : 'Pribadi';

            // Sync dengan project dan simpan peran
            $syncData = [];
            foreach ($data['perorangan_ids'] as $id) {
                $syncData[$id] = ['peran' => $peran];
            }
            $record->perorangan()->sync($syncData);
        }
    }
}
