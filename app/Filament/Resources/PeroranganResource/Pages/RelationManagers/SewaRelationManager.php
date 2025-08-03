<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\DateColumn;

class SewaRelationManager extends RelationManager
{
    protected static string $relationship = 'sewa';
    protected static ?string $title = 'Riwayat Penyewaan';
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        // Tabel ini akan menampilkan proyek yang berelasi dengan customer yang sedang dilihat
        return $table
            ->recordTitleAttribute('judul_sewa')
            ->heading('Riwayat Penyewaan')
            ->columns([
                Tables\Columns\TextColumn::make('judul')->label('Judul Penyewaan'),
                Tables\Columns\TextColumn::make('pivot.peran')
                    ->label('Untuk')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pribadi' => 'success', // hijau
                        default => 'info',   // biru
                    }),
                Tables\Columns\TextColumn::make('rentang')->label('Durasi Sewa'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(fn(string $state): string => match ($state) {
                    'Selesai' => 'success',
                    'Konfirmasi Selesai' => 'info',
                    'Jatuh Tempo' => 'danger',
                    'Belum Selesai' => 'warning',
                    default => 'secondary',
                }),
                Tables\Columns\TextColumn::make('harga_fix')->money('IDR')->default(0),

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
                // Tables\Actions\CreateAction::make(), // Aktifkan jika ingin bisa menambah proyek dari sini
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}