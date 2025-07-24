<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'Jenis Jabatan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Jenis Jabatan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }
}
