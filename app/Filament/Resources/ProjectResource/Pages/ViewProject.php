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
            Actions\Action::make('Acara')
                ->label(
                    fn() =>
                    $this->record->acara
                        ? 'Print Berita Acara'
                        : 'Buat Berita Acara'
                )
                ->url(
                    fn() =>
                    $this->record->acara
                        ? route('acara', [
                            'company' => $this->record->company_id, // atau field company yg benar
                            'id' => $this->record->acara->id,
                        ])
                        : null
                )
                ->openUrlInNewTab()
                ->icon('heroicon-o-document-text')
                ->color(
                    fn() =>
                    $this->record->acara
                        ? 'gray'   // 🔵 biru → sudah ada
                        : 'info'   // 🟠 orange → belum ada
                )
                ->action(function (array $data) {
                    \App\Models\Acara::updateOrCreate(
                        ['project_id' => $this->record->id],
                        []
                    );
                }),
            // ->form($this->beritaAcaraForm())
            // ->mountUsing(function (Forms\ComponentContainer $form) {
            //     if ($this->record->beritaAcara) {
            //         $form->fill(
            //             $this->record->beritaAcara->toArray()
            //         );
            //     }
            // })
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
