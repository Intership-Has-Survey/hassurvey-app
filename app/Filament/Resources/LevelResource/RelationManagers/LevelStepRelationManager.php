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
use Filament\Facades\Filament;

class LevelStepRelationManager extends RelationManager
{
    protected static string $relationship = 'levelsteps';
    protected static ?string $title = 'Tahap Persetujuan';
    // protected static ?string $heading = 'Buat Tahap Persetujuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('step')
                    ->label('Tahap Ke')
                    ->required()
                    ->minValue(1)
                    ->numeric(),

                Forms\Components\Select::make('role_id')
                    ->required()
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn($query) => $query->where('company_id',  Filament::getTenant()->id)
                    )
                    ->preload()
                    ->label('Penyetuju'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('level_id')
            ->columns([
                // Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('step')->label('No')->sortable(),
                Tables\Columns\TextColumn::make('role.name')->label('Penyetuju'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Buat Tahap Persetujuan'),
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
            ])
            ->defaultSort('step', 'asc');
    }
}
