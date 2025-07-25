<?php

namespace App\Filament\Resources\StatusPekerjaanResource\Pages;

use App\Filament\Resources\StatusPekerjaanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatusPekerjaan extends CreateRecord
{
    protected static string $resource = StatusPekerjaanResource::class;

    protected static ?string $title = 'Tambah Status Pekerjaan';

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
