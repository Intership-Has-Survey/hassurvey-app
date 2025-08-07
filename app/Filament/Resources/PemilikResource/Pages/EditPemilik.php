<?php

namespace App\Filament\Resources\PemilikResource\Pages;

use App\Filament\Resources\PemilikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\RiwayatSewa;
use Illuminate\Support\Facades\DB;

use App\Filament\Resources\PemilikResource\Widgets\RingkasanHarga;

class EditPemilik extends EditRecord
{
    protected static string $resource = PemilikResource::class;

    protected static string $view = 'filament.resources.pemilik-resource.pages.edit-pemilik';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove totals from form data as they will be shown in widget
        return $data;
    }

    protected function getContentWidgets(): array
    {
        return [
            RingkasanHarga::class,
        ];
    }

    protected function getContentWidgetsColumns(): int | array
    {
        // Widget akan mengambil lebar penuh (1 kolom).
        // Ini cocok untuk StatsOverviewWidget.
        return 1;
    }
}
