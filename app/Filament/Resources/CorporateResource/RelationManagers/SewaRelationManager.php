<?php

namespace App\Filament\Resources\CorporateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\DateColumn;

class SewaRelationManager extends RelationManager
{
    protected static string $relationship = 'sewa';
    protected static ?string $title = 'Riwayat Penyewaan';
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        // Tabel ini akan menampilkan proyek yang berelasi dengan customer yang sedang dilihat
        return $table
            ->recordTitleAttribute('judul_sewa')
            ->heading('Riwayat Penyewaan')
            ->columns([
                Tables\Columns\TextColumn::make('judul')->label('Judul Penyewaan'),
                Tables\Columns\TextColumn::make('rentang')->label('Durasi Sewa'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(fn(string $state): string => match ($state) {
                    'Selesai' => 'success',
                    'Konfirmasi Selesai' => 'info',
                    'Jatuh Tempo' => 'danger',
                    'Belum Selesai' => 'warning',
                    default => 'secondary',
                }),
                Tables\Columns\TextColumn::make('harga_fix')->money('IDR')->default(0),

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

    // Override to show all records in the databas

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->label('Judul Sewa'),
                Forms\Components\DatePicker::make('tgl_mulai')
                    ->required()
                    ->label('Tanggal Mulai'),
                Forms\Components\DatePicker::make('tgl_selesai')
                    ->label('Tanggal Selesai'),
                Forms\Components\TextInput::make('rentang')
                    ->label('Rentang'),
                Forms\Components\TextInput::make('provinsi')
                    ->label('Provinsi'),
                Forms\Components\TextInput::make('kota')
                    ->label('Kota'),
                Forms\Components\TextInput::make('kecamatan')
                    ->label('Kecamatan'),
                Forms\Components\TextInput::make('desa')
                    ->label('Desa'),
                Forms\Components\Textarea::make('detail_alamat')
                    ->label('Detail Alamat'),
                Forms\Components\TextInput::make('harga_perkiraan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Harga Perkiraan'),
                Forms\Components\TextInput::make('harga_real')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Harga Real'),
                Forms\Components\TextInput::make('harga_fix')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Harga Fix'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Belum Selesai' => 'Belum Selesai',
                        'Selesai' => 'Selesai',
                        'Konfirmasi Selesai' => 'Konfirmasi Selesai',
                        'Jatuh Tempo' => 'Jatuh Tempo'
                    ])
                    ->default('Belum Selesai')
                    ->native(false),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User'),
                Forms\Components\Placeholder::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                Forms\Components\Placeholder::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->content(fn ($record) => $record?->updated_at?->format('d/m/Y H:i') ?? '-'),
            ]);
    }
}
