<?php

namespace App\Filament\Resources\PemilikResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\BadgeColumn;


class DaftarAlatRelationManager extends RelationManager
{
    protected static string $relationship = 'DaftarAlat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_seri')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_alat')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->required(),
                        Forms\Components\TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ])
                    ->required(),
                Forms\Components\Select::make('merk')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])->required(),
            ])->columns(2);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')
                    ->label('Jenis Alat'),
                Tables\Columns\TextColumn::make('merk.nama')
                    ->label('Merk Alat'),
                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Alat'),
                BadgeColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Tersedia' : 'Tidak Tersedia')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'warning',
                    }),
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
