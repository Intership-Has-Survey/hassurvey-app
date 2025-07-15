<?php

// ========================================================================
// FILE 1: app/Filament/Resources/ProjectResource/RelationManagers/PersonelsRelationManager.php
// Kode ini sudah benar dan diatur untuk relasi Many-to-Many.
// ========================================================================
namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class PersonelsRelationManager extends RelationManager
{
    // Nama ini harus cocok dengan nama method relasi di Model Project
    protected static string $relationship = 'personels';

    protected static ?string $title = 'Tim Personel Proyek';

    // Form ini hanya digunakan untuk MENGEDIT data pivot (peran)
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('peran')
                    ->label('Peran di Proyek')
                    ->options([
                        'surveyor' => 'Surveyor',
                        'asisten surveyor' => 'Asisten Surveyor',
                        'driver' => 'Driver',
                        'drafter' => 'Drafter',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('jabatan')->badge(),
                // Menampilkan data 'peran' dari tabel pivot
                Tables\Columns\TextColumn::make('pivot.peran')
                    ->label('Peran di Proyek')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        // Dropdown untuk memilih personel yang sudah ada
                        $action->getRecordSelect(),
                        // Field untuk mengisi data di tabel pivot
                        Forms\Components\Select::make('peran')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                        // Select::make('peran')
                        //     ->label('Jenis Pekerjaan')
                        //     ->options(\App\Models\Kategori::pluck('nama', 'id'))
                        //     ->required()
                        //     ->searchable()
                        //     ->preload(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
