<?php

namespace App\Filament\Resources\DaftarAlatResource\Pages;

use App\Filament\Resources\DaftarAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarAlats extends ListRecords
{
    protected static string $resource = DaftarAlatResource::class;

    protected static ?string $title = 'Daftar Alat';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Alat')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

}
