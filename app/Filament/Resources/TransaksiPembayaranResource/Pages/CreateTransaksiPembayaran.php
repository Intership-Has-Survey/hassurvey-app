<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Resources\TransaksiPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiPembayaran extends CreateRecord
{
    protected static string $resource = TransaksiPembayaranResource::class;

    protected static ?string $title = 'Tambah Transaksi Pembayaran';

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
