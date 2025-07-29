<?php

namespace App\Filament\Resources\PenjualanResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DaftarAlat;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetailPenjualanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenjualan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daftar_alat_id')
                    ->label('Alat')
                    ->options(DaftarAlat::all()->mapWithKeys(fn($alat) => [$alat->id => $alat->nama_alat ?? 'N/A']))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $jumlah = (float) $get('jumlah');
                        $hargaSatuan = (float) $get('harga_satuan');
                        $set('subtotal_item', $jumlah * $hargaSatuan);
                    }),
                Forms\Components\TextInput::make('harga_satuan')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $jumlah = (float) $get('jumlah');
                        $hargaSatuan = (float) $get('harga_satuan');
                        $set('subtotal_item', $jumlah * $hargaSatuan);
                    }),
                Forms\Components\TextInput::make('subtotal_item')
                    ->numeric()
                    ->required()
                    ->readOnly(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('daftar_alat_id')
            ->columns([
                Tables\Columns\TextColumn::make('daftarAlat.nama_alat')
                    ->label('Alat'),
                Tables\Columns\TextColumn::make('jumlah'),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('subtotal_item')
                    ->money('IDR'),
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