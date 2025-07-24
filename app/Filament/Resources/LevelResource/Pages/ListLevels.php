<?php

namespace App\Filament\Resources\LevelResource\Pages;

use App\Filament\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLevels extends ListRecords
{
    protected static string $resource = LevelResource::class;

    protected static ?string $title = 'Jenis Tingkatan Pengajuan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jenis Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Daftar Jenis';
    }


}
