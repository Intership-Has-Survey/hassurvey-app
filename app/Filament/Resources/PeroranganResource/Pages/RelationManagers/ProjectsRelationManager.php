<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Form $form): Form
    {
        // Form untuk membuat/mengedit project dari halaman ini (opsional)
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_project')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        // Tabel ini akan menampilkan proyek yang berelasi dengan customer yang sedang dilihat
        return $table
            ->recordTitleAttribute('nama_project')
            ->heading('Riwayat Proyek Customer Perorangan')
            ->columns([
                Tables\Columns\TextColumn::make('nama_project'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('nilai_project')->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Aktifkan jika ingin bisa menambah proyek dari sini
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}