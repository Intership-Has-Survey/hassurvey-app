<?php

namespace App\Filament\Resources\VisiMatiResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OperasionalsRelationManager extends RelationManager
{
    protected static string $relationship = 'operasionals';

    protected static ?string $title = 'Operasionals';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('target')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('target')->sortable(),
            ])
            ->filters([
                //
            ]);
    }

    public function getTableQuery(): Builder
    {
        return $this->getRelationship()->getQuery();
    }
}
