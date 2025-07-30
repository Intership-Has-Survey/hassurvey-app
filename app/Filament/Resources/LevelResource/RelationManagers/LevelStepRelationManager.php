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
    protected static ?string $title = 'Urutan persetujuan';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('step')
                    ->label('urutan ke')
                    ->numeric(),
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'name')
                    ->preload()
                    ->label('Jabatan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('level_id')
            ->columns([
                // Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('step')->label('urutan pengajuan')->sortable(),
                Tables\Columns\TextColumn::make('role.name')->label('Jabatan'),
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
