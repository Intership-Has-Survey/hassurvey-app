<?php

namespace App\Filament\Resources\PeroranganResource\Pages;

use App\Filament\Resources\PeroranganResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerorangan extends CreateRecord
{
    protected static string $resource = PeroranganResource::class;

    protected static ?string $title = 'Tambah Perorangan';

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
