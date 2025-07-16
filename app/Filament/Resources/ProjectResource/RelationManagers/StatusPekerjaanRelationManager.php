<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatusPekerjaanRelationManager extends RelationManager
{
    protected static string $relationship = 'statusPekerjaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('laporan')
                //     ->required()
                //     ->maxLength(255),
                // Select::make('jenis_Pekerjaan')
                //     ->label('Jenis Pekerjaan')
                //     ->options([
                //         'pekerjaan_lapangan' => 'Pekerjaan Lapangan',
                //         'pekerjaan_data_dan_gambar' => 'Pekerjaan Data dan Gambar',
                //         'laporan' => 'Laporan',
                //     ]),
                Select::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->options([
                        'pekerjaan_lapangan' => 'Pekerjaan Lapangan',
                        'data_gambar' => 'Data dan Gambar',
                        'laporan' => 'Laporan',
                    ])
                    ->required()
                    ->native(false),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Belum Selesai' => 'Belum Selesai',
                        'Selesai' => 'Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ])
                    ->required()
                    ->native(false),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('laporan')
            ->columns([
                Tables\Columns\TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Editor'),
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
