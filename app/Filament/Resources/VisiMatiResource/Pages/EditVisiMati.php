<?php

namespace App\Filament\Resources\VisiMatiResource\Pages;

use App\Filament\Resources\VisiMatiResource;
use App\Models\Operasional;
use App\Models\Tabungan;
use Filament\Resources\Pages\EditRecord;

class EditVisiMati extends EditRecord
{
    protected static string $resource = VisiMatiResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        $subcategories = [];

        foreach ($this->record->tabungans as $tabungan) {
            $subcategories[] = [
                'type' => 'tabungan',
                'nama' => $tabungan->nama,
                'target' => $tabungan->target,
            ];
        }

        foreach ($this->record->operasionals as $operasional) {
            $subcategories[] = [
                'type' => 'operasional',
                'nama' => $operasional->nama,
                'target' => $operasional->target,
            ];
        }

        $this->form->fill([
            ...$this->record->toArray(),
            'subcategorizables' => $subcategories,
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->subcategories = $data['subcategorizables'] ?? [];
        unset($data['subcategorizables']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Hapus data lama
        $this->record->tabungans()->delete();
        $this->record->operasionals()->delete();

        // Simpan ulang dari form
        foreach ($this->subcategories as $item) {
            if ($item['type'] === 'tabungan') {
                $this->record->tabungans()->create([
                    'nama' => $item['nama'],
                    'target' => $item['target'],
                ]);
            }

            if ($item['type'] === 'operasional') {
                $this->record->operasionals()->create([
                    'nama' => $item['nama'],
                    'target' => $item['target'],
                ]);
            }
        }
    }
}
