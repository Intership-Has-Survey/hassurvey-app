<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use App\Filament\Resources\PengajuanDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanDana extends CreateRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected static ?string $title = 'Tambah Pengajuan Dana';

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['tipe_pengajuan'] = 'inhouse'; // Removed as column no longer exists
        return $data;
    }
}
