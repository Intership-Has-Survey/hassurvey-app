<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Pages\RiwayatCustomer;
use Filament\Actions\SaveAction;
use Filament\Actions\CancelAction;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return RiwayatCustomer::getUrl();
    }

    // 3. Tambahkan method ini untuk memperbaiki breadcrumb
    public function getBreadcrumbs(): array
    {
        return [
            // URL ke halaman daftar kustom Anda
            RiwayatCustomer::getUrl() => 'Riwayat Customer',
            // Teks untuk halaman saat ini (tidak bisa diklik)
            '#' => 'Edit',
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->successRedirectUrl(RiwayatCustomer::getUrl()),
        ];
    }

    // v-- TAMBAHKAN SELURUH METHOD INI --vS
    /**
     * Override form actions to control the "Save" and "Cancel" buttons.
     * This is the final fix for the "index route not defined" error.
     */
}
