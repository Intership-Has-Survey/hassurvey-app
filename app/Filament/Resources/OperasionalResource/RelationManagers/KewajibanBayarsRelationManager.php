<?php

namespace App\Filament\Resources\OperasionalResource\RelationManagers;

use App\Models\PenerimaOperasional;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KewajibanBayarsRelationManager extends RelationManager
{
    protected static string $relationship = 'kewajibanBayars';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('penerima_operasional_id')
                    ->label('Penerima Operasional')
                    ->options(PenerimaOperasional::all()->pluck('nama', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->nullable(),
                Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bukti')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('penerimaOperasional.nama')
                    ->label('Penerima'),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('nominal')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('bukti'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
