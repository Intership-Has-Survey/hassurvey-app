<?php

namespace App\Filament\Resources\DaftarAlatResource\Pages;

use App\Filament\Resources\DaftarAlatResource;
use Filament\Actions\Action; // <-- PERUBAHAN DI SINI
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;

class CreateDaftarAlat extends CreateRecord
{
    protected static string $resource = DaftarAlatResource::class;

    protected static ?string $title = 'Tambah Alat';

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
