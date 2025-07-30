<?php

namespace App\Filament\Widgets;

use App\Models\KewajibanBayar;
use App\Models\VisiMati;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class KewajibanBayarTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Kewajiban Bayar Operasional';

    public ?VisiMati $record = null;

    protected function getTableQuery(): Builder
    {
        return KewajibanBayar::query()
            ->whereHas('operasional', function (Builder $query) {
                $query->where('visi_mati_id', $this->record->id);
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('penerimaOperasional.nama')
                ->label('Penerima'),
            Tables\Columns\TextColumn::make('nama'),
            Tables\Columns\TextColumn::make('nominal')
                ->numeric(decimalPlaces: 2)
                ->sortable(),
            Tables\Columns\TextColumn::make('bukti'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // Define actions here if needed
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            // Define header actions here if needed
        ];
    }
}