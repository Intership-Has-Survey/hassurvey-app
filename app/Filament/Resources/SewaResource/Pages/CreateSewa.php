<?php

namespace App\Filament\Resources\SewaResource\Pages;

use App\Filament\Resources\SewaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSewa extends CreateRecord
{
    protected static string $resource = SewaResource::class;

    protected static ?string $title = 'Tambah Sewa';

    public ?string $customerFlowType = null;

    public function getBreadcrumb(): string
    {
        return 'Buat';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan'),
            ...(static::canCreateAnother()
                ? [$this->getCreateAnotherFormAction()->label('Simpan & tambah lagi')]
                : []),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }
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
                    if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
                        $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                    }
                }
            }
        }
    }
}
