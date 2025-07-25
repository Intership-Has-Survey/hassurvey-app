<?php

namespace App\Filament\Resources\DetailPengajuanResource\Pages;

use App\Filament\Resources\DetailPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailPengajuan extends CreateRecord
{
    protected static string $resource = DetailPengajuanResource::class;

    protected static ?string $title = 'Tambah Detail Pengajuan';

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
}
