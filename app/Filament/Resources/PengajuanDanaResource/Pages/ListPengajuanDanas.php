<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PengajuanDanaResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPengajuanDanas extends ListRecords
{
    protected static string $resource = PengajuanDanaResource::class;

    protected static ?string $title = 'Daftar Pengajuan Dana';

    use ExposesTableToWidgets;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Pengajuan Dana')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PengajuanDanaResource\Widgets\PengajuanDanaOverview::class,
        ];
    }
}
