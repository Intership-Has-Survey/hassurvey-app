<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Corporate;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected static ?string $title = 'Tambah Project';

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

        if (empty($data['sewa_id'])) {
            $sewa = \App\Models\Sewa::create([
                'judul' => 'Kontrak Sewa Otomatis untuk ' . $data['nama_project'],
                'tgl_mulai' => now(),
                'tgl_selesai' => now()->addDays(7),
                'provinsi' => $data['provinsi'] ?? '',
                'kota' => $data['kota'] ?? '',
                'kecamatan' => $data['kecamatan'] ?? '',
                'desa' => $data['desa'] ?? '',
                'detail_alamat' => $data['detail_alamat'] ?? '',
                'corporate_id' => $data['corporate_id'] ?? null,
                'user_id' => $data['user_id'],
            ]);

            $data['sewa_id'] = $sewa->getKey();
        }

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

        $project = $this->getRecord();
        $assignedPersonels = $this->form->getState()['assignedPersonels'] ?? [];

        $syncData = [];
        foreach ($assignedPersonels as $item) {
            if (!empty($item['personel_id']) && !empty($item['peran'])) {
                $syncData[$item['personel_id']] = ['peran' => $item['peran']];
            }
        }

        if (!empty($syncData)) {
            $project->personels()->sync($syncData);
        }
    }
}
