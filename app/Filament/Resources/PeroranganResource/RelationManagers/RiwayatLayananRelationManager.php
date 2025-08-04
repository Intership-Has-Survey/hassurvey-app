<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class RiwayatLayananRelationManager extends RelationManager
{
    protected static string $relationship = 'projects'; // We'll override this
    protected static ?string $title = 'Riwayat Layanan';
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // This form won't be used as we're only displaying data
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Riwayat Layanan')
            ->columns([
                TextColumn::make('service_type')
                    ->label('Jenis Layanan')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'project' => 'Proyek Pemetaan',
                        'sewa' => 'Penyewaan Alat',
                        'kalibrasi' => 'Kalibrasi',
                        'penjualan' => 'Penjualan',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'project' => 'info',
                        'sewa' => 'warning',
                        'kalibrasi' => 'success',
                        'penjualan' => 'primary',
                        default => 'secondary',
                    }),
                TextColumn::make('name')
                    ->label('Nama Layanan')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'selesai' => 'success',
                        'dalam_proses' => 'primary',
                        'aktif' => 'success',
                        'Lunas' => 'success',
                        'Belum Lunas' => 'warning',
                        'Belum Dibayar' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('service_type')
                    ->label('Jenis Layanan')
                    ->options([
                        'project' => 'Proyek Pemetaan',
                        'sewa' => 'Penyewaan Alat',
                        'kalibrasi' => 'Kalibrasi',
                        'penjualan' => 'Penjualan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // This filter will be handled in the getTableQuery method
                        return $query;
                    }),
            ])
            ->headerActions([
                // No actions for now
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for now
            ]);
    }

    protected function getTableQuery(): Builder
    {
        // This is a complex query that would need to combine all services
        // For now, we'll return the parent query and handle the logic in the columns
        return parent::getTableQuery();
    }
}
