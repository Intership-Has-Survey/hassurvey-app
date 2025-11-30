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
    //jika sewa dia meurujuk ke sewa dan jika sewar merujuk ke riwayat_sewa
    protected static string $relationship = 'sewar';
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

                // ->sortable(false),
                // Tampilkan info sewa dari relasi
                Tables\Columns\TextColumn::make('sewa.kode_sewa')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->sewa->project) {
                            return $record->sewa->project->kode_project;
                        }
                        return $state ?? 'Kode Sewa Tidak Ada';
                    })
                    ->label('Kode Sewa/Proyek')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sewa.judul')
                    ->label('Digunakan di Proyek/Sewa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->placeholder('Belum Kembali'),

                //jika tidak punya kolom di databse maka perlu getStatusAtributr
                Tables\Columns\TextColumn::make('apakah')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        // Jika ada tgl_masuk, status = Tersedia
                        if (!empty($record->tgl_masuk)) {
                            return 'Tersedia';
                        }
                        // Jika tidak ada tgl_masuk tapi ada tgl_keluar, status = Terpakai
                        else if (!empty($record->tgl_keluar)) {
                            return 'Terpakai';
                        }
                        // Default status
                        return 'Menunggu';
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
                                Column::make('daftarAlat.jenisAlat.nama')->heading('Jenis Alat'),
                                Column::make('daftarAlat.merk.nama')->heading('Merk'),
                                Column::make('daftarAlat.nomor_seri')->heading('Nomor Seri'),
                                Column::make('daftarAlat.pemilik.nama')->heading('Pemilik'),

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
