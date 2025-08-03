<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKalibrasi extends EditRecord
{
    protected static string $resource = KalibrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

    protected function afterSave(): void
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
