<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;

class DetailPengajuansRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPengajuans';
    protected static ?string $title = 'Rincian Pengajuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deskripsi')->required()->columnSpan(2),
                Forms\Components\TextInput::make('qty')->required()->numeric()->default(1),
                Forms\Components\TextInput::make('harga_satuan')->required()->numeric()->prefix('Rp'),
            ])->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
                Tables\Columns\TextColumn::make('deskripsi'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('harga_satuan')->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->state(fn($record) => $record->qty * $record->harga_satuan)
                    ->money('IDR'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($livewire, $record) {
                        $livewire->getOwnerRecord()->updateTotalHarga();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->after(function ($livewire, $record) {
                        $livewire->getOwnerRecord()->updateTotalHarga();
                    }),
                DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->getOwnerRecord()->updateTotalHarga();
                    }),
            ]);
    }
}
