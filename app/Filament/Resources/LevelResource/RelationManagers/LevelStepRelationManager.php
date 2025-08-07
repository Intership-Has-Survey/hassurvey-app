<?php

namespace App\Filament\Resources\LevelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

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
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('role_id')
                    ->required()
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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->recordTitle(fn(Model $record) => "data ini?"), // Ini kuncinya
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
