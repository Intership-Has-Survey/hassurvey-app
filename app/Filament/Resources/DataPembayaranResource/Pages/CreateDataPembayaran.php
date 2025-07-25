<?php

namespace App\Filament\Resources\DataPembayaranResource\Pages;

use App\Filament\Resources\DataPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDataPembayaran extends CreateRecord
{
    protected static string $resource = DataPembayaranResource::class;

    protected static ?string $title = 'Tambah Data Pembayaran';

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
