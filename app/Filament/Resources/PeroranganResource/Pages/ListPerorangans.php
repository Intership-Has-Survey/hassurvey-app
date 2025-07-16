<?php

namespace App\Filament\Resources\PeroranganResource\Pages;

use App\Filament\Resources\PeroranganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerorangans extends ListRecords
{
    protected static string $resource = PeroranganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
