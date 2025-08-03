<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class RiwayatKalibrasisRelationManager extends RelationManager
{
    protected static string $relationship = 'kalibrasi';
    protected static ?string $title = 'Riwayat Kalibrasi';
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->label('Nama Kalibrasi'),
                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Harga'),
                Forms\Components\Select::make('status')
                    ->options([
                        'dalam_proses' => 'Dalam proses',
                        'selesai' => 'Selesai'
                    ])
                    ->default('dalam_proses')
                    ->native(false),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->heading('Riwayat Kalibrasi')
            ->columns([
                TextColumn::make('nama')
                    ->label('Judul Kalibrasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot.peran')
                    ->label('Untuk')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pribadi' => 'success', // hijau
                        default => 'info',   // biru
                    }),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dalam_proses' => 'primary',
                        'selesai' => 'success',
                        default => 'primary'
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'dalam_proses' => 'Dalam Proses',
                        'selesai' => 'Selesai',
                        default => $state
                    }),
                TextColumn::make('harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pembuat'),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
