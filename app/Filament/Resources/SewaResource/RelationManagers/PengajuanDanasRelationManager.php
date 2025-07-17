<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengajuanDanasRelationManager extends RelationManager
{
    protected static string $relationship = 'pengajuanDanas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul_pengajuan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('tipe_pengajuan')
                    ->default('sewa'),
                Forms\Components\Textarea::make('deskripsi_pengajuan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nama_bank')->maxLength(255),
                Forms\Components\TextInput::make('nomor_rekening')->maxLength(255),
                Forms\Components\TextInput::make('nama_pemilik_rekening')->maxLength(255),
                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_pengajuan')
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengajuan'),
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
