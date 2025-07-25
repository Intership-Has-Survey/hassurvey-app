<?php

namespace App\Filament\Resources\SewaResource\Pages;

use App\Filament\Resources\SewaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSewa extends CreateRecord
{
    protected static string $resource = SewaResource::class;

    protected static ?string $title = 'Tambah Sewa';

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
