<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Corporate;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProjectResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }
    protected function afterCreate(): void
    {
        // 1. Ambil data project yang baru saja dibuat
        $project = $this->getRecord();
        // 2. Ambil semua data dari form
        $data = $this->form->getState();

        // 3. Cek apakah ini adalah alur kerja 'corporate' dan perusahaan sudah dipilih
        if (($data['customer_flow_type'] ?? null) === 'corporate' && !empty($data['corporate_id'])) {
            $corporate = Corporate::find($data['corporate_id']);

            if ($corporate && !empty($data['perorangan'])) {
                // 4. Ambil semua ID PIC dari repeater
                $picIds = collect($data['perorangan'])->pluck('perorangan_id')->filter();

                // 5. Hubungkan semua PIC tersebut ke Corporate
                $corporate->perorangans()->syncWithoutDetaching($picIds);
            }
        }
    }
}
