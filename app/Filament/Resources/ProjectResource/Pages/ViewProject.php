<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ProjectResource\Widgets\RingkasanPemetaan;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
// use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            // ExportAction::make()->exports([
            //     ExcelExport::make('project')
            //         ->fromModel($this->record) // hanya 1 baris ini
            //         ->withFilename('Project-' . $this->record->kode_project . '.xlsx'),
            // ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RingkasanPemetaan::class,
        ];
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }
}
