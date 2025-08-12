<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;


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
                    ->validationMessages([
                        'required' => 'Jenis pekerjaan harus dipilih'
                    ])
                    ->native(false),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Belum Selesai' => 'Belum Selesai',
                        'Selesai' => 'Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Status pekerjaan wajib diisi'
                    ])
                    ->native(false),
                FileUpload::make('bukti_pekerjaan_path')
                    ->label('Bukti Pekerjaan')
                    ->image()
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('bukti-pekerjaan')
                    ->columnSpanFull(),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->nullable(),
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
                    ->label('Jenis Pekerjaan')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'pekerjaan_lapangan' => 'Pekerjaan Lapangan',
                            'data_gambar' => 'Data dan Gambar',
                            'laporan' => 'Laporan',
                        ];
                        return $map[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                ImageColumn::make('bukti_pekerjaan_path')
                    ->label('Bukti Pekerjaan')
                    ->disk('public')
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pekerjaan_path ? Storage::disk('public')->url($record->bukti_pekerjaan_path) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime('d M Y H:i'),
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
