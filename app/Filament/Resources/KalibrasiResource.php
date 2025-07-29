<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KalibrasiResource\Pages;
use App\Filament\Resources\KalibrasiResource\RelationManagers;
use App\Models\Kalibrasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KalibrasiResource\RelationManagers\DetailKalibrasiRelationManager;

class KalibrasiResource extends Resource
{
    protected static ?string $model = Kalibrasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Kalibrasi';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Jasa Kalibrasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Kalibrasi')
                    ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Customer')
                            ->required(),
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'progress' => 'progress',
                        'selesai' => 'selesai'
                    ])
                    ->native(false),
                Hidden::make('harga')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('customer_id'),
                Tables\Columns\TextColumn::make('status'),
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
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
        ];
    }
}
