<?php

namespace App\Filament\Resources\PemilikResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // <-- Tambahkan import ini
use Illuminate\Database\Eloquent\Model;

class RiwayatSewaPemilikRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatSewaAlat';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Riwayat Penggunaan Alat';
    protected function getRelationshipQuery(): Builder
    {
        return parent::getRelationshipQuery()->with(['daftarAlat', 'sewa']);
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                // Tampilkan info alat dari relasi
                Tables\Columns\TextColumn::make('daftarAlat.nomor_seri')
                    ->label('Nomor Seri Alat')
                    ->searchable(),

                // Tampilkan info sewa dari relasi
                Tables\Columns\TextColumn::make('sewa.judul')
                    ->label('Digunakan di Proyek Sewa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->placeholder('Belum Kembali'),

                Tables\Columns\TextColumn::make('biaya_sewa_alat_final')
                    ->label('Pendapatan Kotor')
                    ->money('IDR')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('pendapataninv_final')
                    ->label('Pendapatan Investor/Pemilik')
                    ->money('IDR')
                    ->placeholder('-'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
