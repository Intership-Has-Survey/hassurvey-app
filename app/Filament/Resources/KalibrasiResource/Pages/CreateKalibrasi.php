<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use App\Models\Kalibrasi;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKalibrasi extends CreateRecord
{
    protected static string $resource = KalibrasiResource::class;

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
