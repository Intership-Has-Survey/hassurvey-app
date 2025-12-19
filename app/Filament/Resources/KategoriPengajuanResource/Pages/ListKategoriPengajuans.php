<?php

namespace App\Filament\Resources\KategoriPengajuanResource\Pages;

use App\Filament\Resources\KategoriPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPengajuans extends ListRecords
{
    protected static string $resource = KategoriPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('bast-setting')
                ->label('Pengaturan Berita Acara')
                ->url(self::getResource()::getUrl('custom'))
                ->color('info'),
        ];
    }
}
