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

    protected static bool $isLazy = false;

    protected static ?string $title = 'Riwayat Pemetaan';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_project')
            ->heading('Riwayat Pemetaan')
            ->columns([
                Tables\Columns\TextColumn::make('nama_project')->label('Nama Proyek'),
                Tables\Columns\TextColumn::make('status')->badge()->label('Status Proyek'),
                Tables\Columns\TextColumn::make('status_pekerjaan')->badge()->label('Status Pekerjaan'),
                Tables\Columns\TextColumn::make('status_pembayaran')->badge()->label('Status Pembayaran'),
                Tables\Columns\TextColumn::make('nilai_project')->money('IDR')->label('Nilai Proyek'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembuat'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}