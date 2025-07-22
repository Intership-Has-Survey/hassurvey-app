<?php

namespace App\Filament\Resources\LevelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LevelStepRelationManager extends RelationManager
{
    protected static string $relationship = 'levelsteps';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('level_id')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\Select::make('role_id')
                //     ->relationship('levelsteps', 'name')
                //     ->searchable()
                //     ->preload()  
                //     ->label('Kategori Proyek')
                //     ->required(),
                Forms\Components\TextInput::make('step')
                    ->numeric(),
                // Forms\Components\TextInput::make('role_id')
                //     ->numeric(),
                Forms\Components\Select::make('role_id')
                    // ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                // ->label('Assign Roles'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('level_id')
            ->columns([
                // Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('step'),
                Tables\Columns\TextColumn::make('roles.name'),
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
