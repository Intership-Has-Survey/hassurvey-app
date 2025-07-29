<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisiMatiResource\Pages;
use App\Filament\Resources\VisiMatiResource\RelationManagers\OperasionalsRelationManager;
use App\Filament\Resources\VisiMatiResource\RelationManagers\TabungansRelationManager;
use App\Models\VisiMati;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisiMatiResource extends Resource
{
    protected static ?string $model = VisiMati::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Textarea::make('deskripsi')
                    ->nullable(),
                Repeater::make('subcategorizables')
                    ->label('Sub-kategori')
                    ->maxItems(2)
                    ->schema([
                        Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'tabungan' => 'Tabungan',
                                'operasional' => 'Operasional',
                            ])
                            ->required(),
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('target')
                            ->label('Target')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TabungansRelationManager::class,
            OperasionalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisiMatis::route('/'),
            'create' => Pages\CreateVisiMati::route('/create'),
            'edit' => Pages\EditVisiMati::route('/{record}/edit'),
        ];
    }
}
