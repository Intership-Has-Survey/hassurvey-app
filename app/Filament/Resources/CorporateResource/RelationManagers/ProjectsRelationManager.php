<?php

namespace App\Filament\Resources\CorporateResource\RelationManagers;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_project')
                    ->label('Nama Project'),
                Forms\Components\Select::make('kategori_id')
                    ->relationship('kategori', 'nama')
                    ->label('Kategori'),
                Forms\Components\Select::make('sales_id')
                    ->relationship('sales', 'nama')
                    ->label('Sales'),
                Forms\Components\DatePicker::make('tanggal_informasi_masuk')
                    ->label('Tanggal Informasi Masuk'),
                Forms\Components\TextInput::make('sumber')
                    ->label('Sumber'),
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
                Forms\Components\TextInput::make('nilai_project_awal')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Nilai Project Awal'),
                Forms\Components\Toggle::make('dikenakan_ppn')
                    ->label('Dikenakan PPN'),
                Forms\Components\TextInput::make('nilai_ppn')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Nilai PPN'),
                Forms\Components\TextInput::make('nilai_project')
                    ->numeric()
                    ->prefix('Rp ')
                    ->label('Nilai Project'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal'
                    ])
                    ->native(false),
                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'Belum Dibayar' => 'Belum Dibayar',
                        'Dibayar Sebagian' => 'Dibayar Sebagian',
                        'Lunas' => 'Lunas'
                    ])
                    ->default('Belum Dibayar')
                    ->native(false),
                Forms\Components\Select::make('status_pekerjaan')
                    ->options([
                        'Belum Dikerjakan' => 'Belum Dikerjakan',
                        'Dalam Pengerjaan' => 'Dalam Pengerjaan',
                        'Selesai' => 'Selesai'
                    ])
                    ->default('Belum Dikerjakan')
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

    // Override to show all records in the database

}
