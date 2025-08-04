<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KewajibanBayarResource\Pages;
use App\Filament\Resources\KewajibanBayarResource\RelationManagers;
use App\Models\KewajibanBayar;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class KewajibanBayarResource extends Resource
{
    protected static ?string $model = KewajibanBayar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Judul Kewajiban Bayar'),
                TextInput::make('deskripsi')
                    ->label('Deskripsi Kewajiban Bayar'),
                TextInput::make('nominal')
                    ->label('Nilai')
                    ->numeric()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                FileUpload::make('bukti')
                    ->label('Bukti Pembayaran')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('nominal')
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKewajibanBayars::route('/'),
            'create' => Pages\CreateKewajibanBayar::route('/create'),
            'edit' => Pages\EditKewajibanBayar::route('/{record}/edit'),
        ];
    }
}
