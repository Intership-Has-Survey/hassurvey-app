<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\DaftarAlat;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Get;

class DaftarAlatProjectRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                TextColumn::make('jenis_alat')->label('Nomor Seri')->searchable(),
                TextColumn::make('nomor_seri')->label('Nomor Seri')->searchable(),
                TextColumn::make('daftar_alat_id')->label('Nomor Seri')->searchable(),
                TextColumn::make('sewa_id')->label('Nomor Seri')->searchable(),

                BadgeColumn::make('kondisi')
                    ->label('Kondisi Master')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state) => $state ? 'success' : 'danger'),

                TextColumn::make('pivot.tgl_keluar') // ambil dari pivot
                    ->label('Tanggal Keluar')
                    ->date('d-m-Y'),

                TextColumn::make('pivot.tgl_masuk') // ambil dari pivot
                    ->label('Tanggal Masuk')
                    ->date('d-m-Y')
                    ->placeholder('Belum Kembali'),

                TextColumn::make('pivot.harga_perhari') // ambil dari pivot
                    ->label('Harga / Hari')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('pivot.biaya_sewa_alat') // ambil dari pivot
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Tambah Alat')
                    ->modalHeading('Tambah Alat ke Proyek')
                    ->preloadRecordSelect()
                    ->using(function (RelationManager $livewire, Model $record, array $data): void {
                        $project = $livewire->ownerRecord;
                        $pivotData = collect($data)->only([
                            'tgl_keluar',
                            'harga_perhari',
                            'user_id',
                        ])->toArray();
                        $pivotData['sewa_id'] = $project->sewa_id;
                        $livewire->getRelationship()->attach($record, $pivotData);
                        $record->update(['status' => false]);
                    })
                    ->after(fn(Model $record) => $record->update(['status' => false]))
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Select::make('jenis_alat_filter')
                            ->label('Pilih Jenis Alat')
                            ->options(DaftarAlat::pluck('jenis_alat', 'jenis_alat')->unique())
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('recordId')
                            ->label('Pilih Nomor Seri')
                            ->options(function (Get $get): array {
                                $jenisAlat = $get('jenis_alat_filter');
                                if (!$jenisAlat)
                                    return [];
                                $alreadyAttachedAlatIds = $this->getOwnerRecord()->daftarAlat()->pluck('daftar_alat.id');
                                return DaftarAlat::query()
                                    ->where('jenis_alat', $jenisAlat)
                                    ->where('status', true)
                                    ->where('kondisi', true)
                                    ->whereNotIn('id', $alreadyAttachedAlatIds)
                                    ->pluck('nomor_seri', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->required()
                            ->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        Forms\Components\DatePicker::make('tgl_keluar')
                            ->label('Tanggal Keluar')
                            ->default(now())
                            ->required()
                            ->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        Forms\Components\TextInput::make('harga_perhari')
                            ->label('Harga Sewa Per Hari')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        Hidden::make('sewa_id')->default(fn(RelationManager $livewire) => $livewire->ownerRecord->sewa_id),
                        Hidden::make('user_id')
                            ->default(auth()->id())

                    ])
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
