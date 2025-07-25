<?php

namespace App\Filament\Resources\StatusPembayaranResource\Pages;

use App\Filament\Resources\StatusPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatusPembayaran extends CreateRecord
{
    protected static string $resource = StatusPembayaranResource::class;

    protected static ?string $title = 'Tambah Status Pembayaran';

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
