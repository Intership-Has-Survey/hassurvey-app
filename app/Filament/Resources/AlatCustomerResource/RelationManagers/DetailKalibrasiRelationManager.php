<?php

namespace App\Filament\Resources\AlatCustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailKalibrasiRelationManager extends RelationManager
{
    protected static string $relationship = 'kalibrasis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now())
                    ->visible('edit')
                    ->native(false),
                Forms\Components\DatePicker::make('tgl_stiker_kalibrasi')
                    ->label('Tanggal Stiker Kalibrasi')
                    ->default(now())
                    ->native(false),
                Forms\Components\DatePicker::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->default(now())
                    ->native(false),
                Forms\Components\Select::make('status')
                    ->visibleOn('edit')
                    ->options([
                        'belum_dikerjakan' => 'Belum dikerjakan',
                        'proses' => 'Dalam proses',
                        'kalibrasi_diluar' => 'Kalibrasi diluar HAS',
                        'sudah_diservis' => 'Sudah diservis',
                        'terkalibrasi' => 'Terkalibrasi'
                    ])
                    ->default('belum_dikerjakan')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('tgl_masuk'),
                Tables\Columns\TextColumn::make('tgl_stiker_kalibrasi'),
                Tables\Columns\TextColumn::make('tgl_keluar'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
