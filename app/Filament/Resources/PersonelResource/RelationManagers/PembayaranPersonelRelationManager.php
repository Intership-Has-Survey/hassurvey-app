<?php

namespace App\Filament\Resources\PersonelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembayaranPersonelRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayaranPersonel';
    // protected static string $relationship = 'PembayaranPersonel';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('personel_project_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payable_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payable_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nilai')
                    ->numeric(),
                Forms\Components\FileUpload::make('bukti_pembayaran'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('personel_project_id'),
                Tables\Columns\TextColumn::make('user.name'),
                // Forms\Components\TextInput::make('personel_project_id')
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
