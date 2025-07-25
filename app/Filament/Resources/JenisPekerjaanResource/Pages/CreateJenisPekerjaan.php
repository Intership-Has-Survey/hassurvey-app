<?php

namespace App\Filament\Resources\JenisPekerjaanResource\Pages;

use App\Filament\Resources\JenisPekerjaanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisPekerjaan extends CreateRecord
{
    protected static string $resource = JenisPekerjaanResource::class;

    protected static ?string $title = 'Tambah Jenis Pekerjaan';

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
