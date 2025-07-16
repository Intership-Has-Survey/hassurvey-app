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

    public function form(Form $form): Form
    {
        // Form untuk membuat/mengedit project dari halaman ini (opsional)
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->label('Judul Penyewaan'),
                Forms\Components\DatePicker::make('tgl_mulai')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_selesai')
                    ->required()
                    ->minDate(fn(Get $get) => $get('tgl_mulai')),
                Forms\Components\TextInput::make('lokasi')
                    ->required(),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
            ]);

    }

    public function table(Table $table): Table
    {
        // Tabel ini akan menampilkan proyek yang berelasi dengan customer yang sedang dilihat
        return $table
            ->recordTitleAttribute('judul_sewa')
            ->heading('Riwayat Proyek Customer Perorangan')
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('tgl_mulai')->date(),
                Tables\Columns\TextColumn::make('tgl_selesai')->date(),
                Tables\Columns\TextColumn::make('lokasi'),
                Tables\Columns\TextColumn::make('total_biaya')->money('IDR'),

                Tables\Columns\TextColumn::make('created_at')->dateTime(),
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