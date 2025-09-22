<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TabunganResource\Pages;
use App\Filament\Resources\TabunganResource\RelationManagers;
use App\Models\Tabungan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TabunganResource extends Resource
{
    protected static ?string $model = Tabungan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return true; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('target_nominal')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('target_tipe')
                    ->options([
                        'orang' => 'Orang',
                        'bangunan' => 'Bangunan',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('target_nominal')->numeric(decimalPlaces: 2)->sortable(),
                Tables\Columns\TextColumn::make('target_tipe')->sortable(),
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
            RelationManagers\PemasukansRelationManager::class,
            RelationManagers\PengeluaransRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTabungans::route('/'),
            'create' => Pages\CreateTabungan::route('/create'),
            'edit' => Pages\EditTabungan::route('/{record}/edit'),
        ];
    }
}
