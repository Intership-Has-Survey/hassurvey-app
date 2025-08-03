<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class RiwayatPenjualansRelationManager extends RelationManager
{
    protected static string $relationship = 'penjualans';
    protected static ?string $title = 'Riwayat Penjualan';
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_penjualan')
                    ->required()
                    ->label('Nama Penjualan'),
                Forms\Components\DatePicker::make('tanggal_penjualan')
                    ->required()
                    ->label('Tanggal Penjualan'),
                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                        'Belum Dibayar' => 'Belum Dibayar'
                    ])
                    ->default('Belum Dibayar')
                    ->native(false),
                Forms\Components\Textarea::make('catatan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_penjualan')
            ->heading('Riwayat Penjualan')
            ->columns([
                TextColumn::make('nama_penjualan')
                    ->label('Nama Penjualan')
                    ->searchable(),
                TextColumn::make('total_items')
                    ->label('Total')
                    ->money('IDR')
                    ->state(function (\App\Models\Penjualan $record): string {
                        return $record->detailPenjualan->sum('harga');
                    }),
                BadgeColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'warning',
                        'Belum Dibayar' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('tanggal_penjualan')
                    ->date()
                    ->label('Tanggal Penjualan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembuat'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
