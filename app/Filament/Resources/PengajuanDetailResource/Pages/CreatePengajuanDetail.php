<?php

namespace App\Filament\Resources\PengajuanDetailResource\Pages;

use App\Filament\Resources\PengajuanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanDetail extends CreateRecord
{
    protected static string $resource = PengajuanDetailResource::class;

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
