<?php

namespace App\Filament\Widgets;

use App\Models\Pemasukan;
use App\Models\VisiMati;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PemasukanTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Pemasukan Tabungan';

    public ?VisiMati $record = null;

    protected function getTableQuery(): Builder
    {
        return Pemasukan::query()
            ->whereHas('tabungan', function (Builder $query) {
                $query->where('visi_mati_id', $this->record->id);
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('jumlah')
                ->numeric(decimalPlaces: 2)
                ->sortable(),
            Tables\Columns\TextColumn::make('deskripsi'),
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