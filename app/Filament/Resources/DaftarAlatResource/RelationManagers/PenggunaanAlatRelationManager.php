<?php

namespace App\Filament\Resources\DaftarAlatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Pages\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenggunaanAlatRelationManager extends RelationManager
{
    protected static string $relationship = 'sewa';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Riwayat Penggunaan Alat';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan info alat dari relasi

                // Tampilkan info sewa dari relasi
                Tables\Columns\TextColumn::make('judul')
                    ->label('Digunakan di Proyek/Sewa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->placeholder('Belum Kembali'),

                Tables\Columns\TextColumn::make('daftarAlat.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Dipakai',
                        1 => 'Selesai',
                        2 => 'Terjual',
                        default => '-',
                    })
                    ->color(fn($state) => match ($state) {
                        0 => 'warning',   // Dipakai
                        1 => 'success',   // Tersedia
                        2 => 'danger',    // Terjual
                        default => 'gray',
                    })



            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
