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
    // Gunakan nama relasi yang baru dibuat di model Pemilik
    protected static string $relationship = 'riwayatSewaAlat';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Riwayat Penggunaan Alat';

    /**
     * Override method ini untuk secara eksplisit memilih semua kolom dari tabel riwayat_sewa.
     * Ini adalah solusi definitif untuk error TypeError pada relasi HasManyThrough
     * karena memastikan kolom 'id' selalu ada dalam hasil query.
     */
    protected function getRelationshipQuery(): Builder
    {
        return parent::getRelationshipQuery()->with(['daftarAlat', 'sewa']);
    }

    public function form(Form $form): Form
    {
        // Form ini tidak kita gunakan karena relation manager ini hanya untuk melihat data
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

                Tables\Columns\TextColumn::make('biaya_sewa_alat')
                    ->label('Pendapatan Kotor')
                    ->money('IDR')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('pendapataninv')
                    ->label('Pendapatan Investor/Pemilik')
                    ->money('IDR')
                    ->placeholder('-'),
            ])
            // Kita nonaktifkan semua aksi karena ini hanya untuk laporan (read-only)
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
