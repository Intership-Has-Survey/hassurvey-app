<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\CorporateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCorporate extends CreateRecord
{
    protected static string $resource = CorporateResource::class;

    protected static ?string $title = 'Tambah Corporate';

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
