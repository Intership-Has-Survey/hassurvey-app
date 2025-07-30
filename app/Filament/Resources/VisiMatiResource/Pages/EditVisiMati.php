<?php

namespace App\Filament\Resources\VisiMatiResource\Pages;

use App\Filament\Resources\VisiMatiResource;
use App\Filament\Widgets\KewajibanBayarTableWidget;
use App\Filament\Widgets\PemasukanTableWidget;
use App\Filament\Widgets\PenerimaOperasionalTableWidget;
use App\Filament\Widgets\PengeluaranTableWidget;
use Filament\Widgets\Tabs;
use Filament\Actions;

use Filament\Resources\Pages\EditRecord;

class EditVisiMati extends EditRecord
{
    protected static string $resource = VisiMatiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['sub_kategori'] = [];

        if ($this->record->tabungan) {
            $data['sub_kategori'][] = 'tabungan';
            $data['tabungan'] = $this->record->tabungan->toArray();
        }

        if ($this->record->operasional) {
            $data['sub_kategori'][] = 'operasional';
            $data['operasional'] = $this->record->operasional->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle Tabungan
        if (in_array('tabungan', $data['sub_kategori'])) {
            if ($this->record->tabungan) {
                $this->record->tabungan->update($data['tabungan']);
            } else {
                $this->record->tabungan()->create($data['tabungan']);
            }
        } else {
            // If tabungan was previously selected but now deselected, delete it
            if ($this->record->tabungan) {
                $this->record->tabungan->delete();
            }
        }

        // Handle Operasional
        if (in_array('operasional', $data['sub_kategori'])) {
            if ($this->record->operasional) {
                $this->record->operasional->update($data['operasional']);
            } else {
                $this->record->operasional()->create($data['operasional']);
            }
        } else {
            // If operasional was previously selected but now deselected, delete it
            if ($this->record->operasional) {
                $this->record->operasional->delete();
            }
        }

        // Remove sub_kategori, tabungan, and operasional from main data to prevent mass assignment issues
        unset($data['sub_kategori']);
        unset($data['tabungan']);
        unset($data['operasional']);

        return $data;
    }

    protected function getFooterWidgets(): array
    {
        $widgets = [];

        if ($this->record->tabungan) {
            $widgets[] = PemasukanTableWidget::make(['record' => $this->record]);
            $widgets[] = PengeluaranTableWidget::make(['record' => $this->record]);
        }

        if ($this->record->operasional) {
            $widgets[] = KewajibanBayarTableWidget::make(['record' => $this->record]);
            $widgets[] = PenerimaOperasionalTableWidget::make(['record' => $this->record]);
        }

        return $widgets;
    }
}
