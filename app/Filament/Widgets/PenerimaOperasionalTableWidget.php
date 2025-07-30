<?php

namespace App\Filament\Widgets;

use App\Models\PenerimaOperasional;
use App\Models\VisiMati;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PenerimaOperasionalTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Penerima Operasional';

    public ?VisiMati $record = null;

    protected function getTableQuery(): Builder
    {
        return PenerimaOperasional::query()
            ->whereHas('operasional', function (Builder $query) {
                $query->where('visi_mati_id', $this->record->id);
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama'),
            Tables\Columns\TextColumn::make('alamat'),
            Tables\Columns\TextColumn::make('keterangan'),
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