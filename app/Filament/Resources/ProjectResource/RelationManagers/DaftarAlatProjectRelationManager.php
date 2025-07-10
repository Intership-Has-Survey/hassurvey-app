<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\DaftarAlat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;

class DaftarAlatProjectRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_alat')
            ->columns([
                Tables\Columns\TextColumn::make('nama_alat')->searchable(),
                Tables\Columns\TextColumn::make('jenis_alat')->label('Jenis Alat'),
                Tables\Columns\TextColumn::make('merk'),
                Tables\Columns\TextColumn::make('kondisi'),
                // Kolom status ini sekarang akan selalu sinkron karena logika baru kita
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'Tidak Tersedia' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')->options(['GPS' => 'GPS', 'Drone' => 'Drone', 'OTS' => 'OTS'])->multiple(),
                SelectFilter::make('status')->options(['Tersedia' => 'Tersedia', 'Tidak Tersedia' => 'Tidak Tersedia']),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Tambah Alat')
                    ->modalHeading('Tambah Alat ke Proyek')
                    ->preloadRecordSelect()
                    // Logika setelah alat berhasil ditambahkan
                    ->after(fn(Model $record) => $record->update(['status' => 'Tidak Tersedia']))
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Pilih Alat')
                            ->required()
                            // LOGIKA PALING PENTING: Menampilkan alat yang BENAR-BENAR tersedia.
                            ->getSearchResultsUsing(function (string $search) {
                                // Tampilkan alat yang statusnya 'Tersedia' DAN namanya cocok dengan pencarian
                                return DaftarAlat::where('status', 'Tersedia')
                                    ->where('nama_alat', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('nama_alat', 'id');
                            })
                            ->getOptionLabelUsing(fn($value): ?string => DaftarAlat::find($value)?->nama_alat),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    // LOGIKA PINTAR SETELAH DETACH
                    ->after(function (Model $record) {
                        // Cek apakah alat ini masih terikat pada proyek aktif lainnya.
                        $isStillInUse = $record->projects()
                            ->where('status_pekerjaan_lapangan', '!=', 'Selesai')
                            ->exists();
                        // Jika SUDAH TIDAK dipakai di mana pun, baru set statusnya jadi Tersedia.
                        if (!$isStillInUse) {
                            $record->update(['status' => 'Tersedia']);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->after(function (Collection $records) {
                            foreach ($records as $record) {
                                // Logika yang sama dengan DetachAction tunggal
                                $isStillInUse = $record->projects()
                                    ->where('status_pekerjaan_lapangan', '!=', 'Selesai')
                                    ->exists();
                                if (!$isStillInUse) {
                                    $record->update(['status' => 'Tersedia']);
                                }
                            }
                        }),
                ]),
            ]);
    }
}
