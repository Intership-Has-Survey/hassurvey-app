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
use Filament\Tables\Columns\TextColumn;

class DetailPenjualanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenjualan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_alat_id')
                    ->label('Jenis Alat')
                    ->options(\App\Models\JenisAlat::all()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('nomor_seri')
                    ->label('Nomor Seri')
                    ->options(function (Get $get) {
                        $jenisAlatId = $get('jenis_alat_id');
                        if (!$jenisAlatId) {
                            return [];
                        }
                        return \App\Models\DaftarAlat::where('jenis_alat_id', $jenisAlatId)->pluck('nomor_seri', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $daftarAlat = \App\Models\DaftarAlat::find($state);
                        $merkNama = $daftarAlat ? $daftarAlat->merk->nama : '';
                        $set('merk_nama', $merkNama);
                        $set('merk_id', $daftarAlat ? $daftarAlat->merk_id : null);
                        $set('daftar_alat_id', $state);
                    }),

                Forms\Components\Select::make('merk_id')
                    ->label('Merek Alat')
                    ->hidden()
                    ->disabled()
                    ->dehydrated(true),

                Forms\Components\TextInput::make('merk_nama')
                    ->label('Merek Alat')
                    ->disabled()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, callable $get) {
                        // no action needed here, just to trigger reactive update
                    })
                    ->dehydrated(false)
                    ->default(''),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('daftar_alat_id')
            ->columns([
                TextColumn::make('jenisAlat.nama')
                    ->label('Jenis Alat'),
                TextColumn::make('daftarAlat.nomor_seri')
                    ->label('Nomor Seri'),
                TextColumn::make('merk.nama')
                    ->label('Merek Alat'),
                TextColumn::make('harga')
                    ->label('Harga'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
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
