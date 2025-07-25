<?php

namespace App\Filament\Resources\SewaResource\Pages;

use App\Filament\Resources\SewaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSewa extends ListRecords
{
    protected static string $resource = SewaResource::class;

    protected static ?string $title = 'Layanan Sewa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Penyewaan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

}
