<?php

namespace App\Filament\Resources\DaftarAlatResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Resources\RelationManagers\RelationManager;
use pxlrbt\FilamentExcel\Columns\Column;

class PenggunaanAlatRelationManager extends RelationManager
{
    protected static string $relationship = 'sewa';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Riwayat Penggunaan Alat';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan info alat dari relasi

                // Tampilkan info sewa dari relasi
                Tables\Columns\TextColumn::make('judul')
                    ->label('Digunakan di Proyek/Sewa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->placeholder('Belum Kembali'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        // Jika ada tgl_keluar, status = Selesai
                        // dd($record);
                        if (!empty($record->tgl_masuk)) {
                            return 'Selesai';
                        }
                        // Jika tidak ada tgl_keluar, status = Terpakai
                        else {
                            return 'Terpakai';
                        }
                    })
                    ->color(function ($state, $record) {
                        // Jika ada tgl_keluar, warna hijau (Selesai)
                        if (!empty($record->tgl_masuk)) {
                            return 'success';
                        }
                        // Jika tidak ada tgl_keluar, warna kuning/orange (Terpakai)
                        else {
                            return 'warning';
                        }
                    })



            ])
            ->headerActions([
                ExportAction::make('semua')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withColumns([
                                // Column::make('kategori.nama')->heading('Kategori'),

                            ])
                            ->withFilename(date('Y-m-d') . ' - projects-export')
                    ])
            ])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
