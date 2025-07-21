<?php

namespace App\Filament\Resources\Shared;

use App\Models\DaftarAlat;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;
use Filament\Forms\Components\FileUpload;

/**
 * Kelas dasar (abstract) untuk mengelola relasi alat pada sewa.
 * Logika ini digunakan bersama oleh SewaResource dan ProjectResource.
 * Simpan file ini di app/Filament/Resources/Shared/BaseAlatSewaRelationManager.php
 */
abstract class BaseAlatSewaRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';

    protected static bool $isLazy = false;

    abstract protected function getSewaRecord(): Sewa;

    public function form(Form $form): Form
    {
        // ... (Tidak ada perubahan di sini)
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tgl_keluar')
                    ->label('Tanggal Alat Keluar')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Masuk (diisi saat pengembalian)')
                    ->minDate(fn(Get $get) => $get('tgl_keluar'))
                    ->live(),
                Forms\Components\TextInput::make('harga_perhari')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\Select::make('kondisi_kembali')
                    ->label('Kondisi Alat Saat Dikembalikan')
                    ->options([
                        'Baik' => 'Baik',
                        'Bermasalah' => 'Bermasalah',
                    ])
                    ->required()
                    ->default('Baik'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan Pengembalian (Opsional)')
                    ->columnSpanFull(),
                FileUpload::make('foto_bukti')
                    ->label('Foto Bukti Pengembalian')
                    ->image()
                    ->directory('bukti-pengembalian')
                    ->required(fn(Get $get): bool => filled($get('tgl_masuk')))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                TextColumn::make('nomor_seri')->searchable(),
                BadgeColumn::make('kondisi')
                    ->label('Kondisi Alat')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state) => $state ? 'success' : 'danger'),
                TextColumn::make('tgl_keluar')->date('d-m-Y'),
                TextColumn::make('tgl_masuk')->date('d-m-Y')->placeholder('Belum Kembali'),
                TextColumn::make('harga_perhari')->money('IDR')->sortable(),

                TextColumn::make('biaya_perkiraan_alat')
                    ->label('Biaya Perkiraan')
                    ->money('IDR')
                    ->state(function (Model $record): ?float {
                        $sewa = $this->getSewaRecord();
                        $pivotData = $record->pivot;

                        if ($sewa?->tgl_selesai && $pivotData?->tgl_keluar && $pivotData?->harga_perhari) {
                            $tglSelesaiKontrak = Carbon::parse($sewa->tgl_selesai);
                            $tglKeluarAlat = Carbon::parse($pivotData->tgl_keluar);

                            if ($tglSelesaiKontrak->gte($tglKeluarAlat)) {
                                $durasiPerkiraan = $tglKeluarAlat->diffInDays($tglSelesaiKontrak) + 1;
                                return $durasiPerkiraan * $pivotData->harga_perhari;
                            }
                        }
                        return 0;
                    }),

                TextColumn::make('biaya_sewa_alat')
                    ->label('Biaya Realisasi')
                    ->money('IDR')
                    ->placeholder('Belum Kembali')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Alat Sewa')
                    ->preloadRecordSelect()
                    ->using(function (array $data, RelationManager $livewire): void {
                        $sewa = $livewire->getSewaRecord();
                        // Menggunakan 'recordId' yang merupakan standar Filament
                        $recordId = $data['recordId'];

                        $data['sewa_id'] = $sewa->id;

                        $livewire->getRelationship()->attach($recordId, $data);

                        $alat = DaftarAlat::find($recordId);
                        if ($alat) {
                            $alat->update(['status' => false]);
                        }
                    })
                    ->form(function (): array {
                        $sewa = $this->getSewaRecord();
                        $alreadyAttachedAlatIds = $sewa->daftarAlat()->pluck('daftar_alat.id');

                        return [
                            // --- PERUBAHAN 1 ---
                            // Field ini hanya untuk UI, tidak untuk disimpan ke database.
                            Forms\Components\Select::make('jenis_alat_filter')
                                ->label('Pilih Jenis Alat')
                                ->options(DaftarAlat::pluck('jenis_alat', 'jenis_alat')->unique())
                                ->live()
                                ->required()
                                ->dehydrated(false), // <-- INI KUNCINYA
            
                            // --- PERUBAHAN 2 ---
                            // Menggunakan 'recordId' sebagai nama field, ini adalah standar untuk AttachAction.
                            Forms\Components\Select::make('recordId')
                                ->label('Pilih Nomor Seri')
                                ->options(function (Get $get) use ($alreadyAttachedAlatIds): array {
                                    $jenisAlat = $get('jenis_alat_filter');
                                    if (!$jenisAlat)
                                        return [];

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

                            Forms\Components\DatePicker::make('tgl_keluar')->label('Tanggal Keluar')->default(now())->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                            Forms\Components\TextInput::make('harga_perhari')->label('Harga Sewa Per Hari')->numeric()->prefix('Rp')->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        ];
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kembalikan')
                    ->visible(fn(Model $record): bool => is_null($record->pivot->tgl_masuk))
                    ->using(function (Model $record, array $data): Model {
                        if (!empty($data['tgl_masuk'])) {
                            $tglKeluar = Carbon::parse($record->pivot->tgl_keluar);
                            $tglMasuk = Carbon::parse($data['tgl_masuk']);
                            $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                            $biayaSewa = $durasiSewa * $data['harga_perhari'];

                            $record->pivot->tgl_masuk = $data['tgl_masuk'];
                            $record->pivot->harga_perhari = $data['harga_perhari'];
                            $record->pivot->biaya_sewa_alat = $biayaSewa;
                            $record->pivot->keterangan = $data['keterangan'];
                            $record->pivot->foto_bukti = $data['foto_bukti'];
                            $record->pivot->save();

                            $kondisiBaru = ($data['kondisi_kembali'] === 'Bermasalah') ? false : true;
                            $record->update(['status' => true, 'kondisi' => $kondisiBaru]);
                        }
                        return $record;
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('batalkan')
                    ->visible(function (Model $record) {
                        if (!is_null($record->pivot->tgl_masuk)) {
                            return false;
                        }
                        $today = today();
                        $createdAt = Carbon::parse($record->pivot->created_at)->startOfDay();
                        return $today->equalTo($createdAt);
                    })
                    ->after(function (Model $record) {
                        $record->update(['status' => true]);
                    }),
                Tables\Actions\Action::make('info')
                    ->label('Tidak bisa dibatalkan')
                    ->disabled()
                    ->color('gray')
                    ->visible(function (Model $record) {
                        if (!is_null($record->pivot->tgl_masuk)) {
                            return false;
                        }
                        $today = today();
                        $createdAt = Carbon::parse($record->pivot->created_at)->startOfDay();
                        return !$today->equalTo($createdAt);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->after(
                            fn(\Illuminate\Database\Eloquent\Collection $records) =>
                            $records->each(fn(Model $record) => $record->update(['status' => true]))
                        ),
                ]),
            ]);
    }
}
