<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected static ?string $title = 'Layanan Proyek Pemetaan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Proyek Pemetaan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }
}
