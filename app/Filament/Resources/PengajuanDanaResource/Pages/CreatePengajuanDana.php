<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use App\Filament\Resources\PengajuanDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanDana extends CreateRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected static ?string $title = 'Tambah Pengajuan Dana';

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

    protected function afterCreate(): void
    {
        $record = $this->record;

        if (!$record->pengajuanable_id) {
            $record->update([
                'pengajuanable_id' => $record->id,
                'pengajuanable_type' => \App\Models\PengajuanDana::class,
            ]);
        }
    }
}
