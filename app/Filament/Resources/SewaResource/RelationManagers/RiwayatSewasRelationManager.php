<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use App\Models\DaftarAlat;
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

class RiwayatSewasRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
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
                    ->label('Kondisi Master')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state) => $state ? 'success' : 'danger'),
                TextColumn::make('tgl_keluar')->date('d-m-Y'),
                TextColumn::make('tgl_masuk')->date('d-m-Y')->placeholder('Belum Kembali'),
                TextColumn::make('harga_perhari')->money('IDR')->sortable(),

                // --- PERUBAHAN DI SINI: Kalkulasi Biaya Perkiraan secara dinamis ---
                TextColumn::make('biaya_perkiraan_alat')
                    ->label('Biaya Perkiraan')
                    ->money('IDR')
                    ->state(function (Model $record): ?float {
                        // Mengambil record Sewa (parent)
                        $sewa = $this->getOwnerRecord();
                        $pivotData = $record->pivot;

                        // Pastikan semua data yang dibutuhkan ada
                        if ($sewa->tgl_selesai && $pivotData->tgl_keluar && $pivotData->harga_perhari) {
                            $tglSelesaiKontrak = Carbon::parse($sewa->tgl_selesai);
                            $tglKeluarAlat = Carbon::parse($pivotData->tgl_keluar);

                            if ($tglSelesaiKontrak->gte($tglKeluarAlat)) {
                                $durasiPerkiraan = $tglKeluarAlat->diffInDays($tglSelesaiKontrak) + 1;
                                return $durasiPerkiraan * $pivotData->harga_perhari;
                            }
                        }
                        // Fallback ke nilai yang tersimpan di database jika ada
                        return $pivotData->biaya_perkiraan_alat ?? 0;
                    }),

                TextColumn::make('biaya_sewa_alat')
                    ->label('Biaya Realisasi')
                    ->money('IDR')
                    ->placeholder('Belum Kembali')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Alat Sewa')
                    ->preloadRecordSelect()
                    ->using(function (array $data): void {
                        $sewa = $this->getOwnerRecord();
                        $recordId = $data['recordId'];

                        // Kalkulasi biaya perkiraan saat pertama kali attach
                        if ($sewa->tgl_selesai && !empty($data['tgl_keluar']) && !empty($data['harga_perhari'])) {
                            $tglSelesaiKontrak = Carbon::parse($sewa->tgl_selesai);
                            $tglKeluarAlat = Carbon::parse($data['tgl_keluar']);

                            if ($tglSelesaiKontrak->gte($tglKeluarAlat)) {
                                $durasiPerkiraan = $tglKeluarAlat->diffInDays($tglSelesaiKontrak) + 1;
                                $data['biaya_perkiraan_alat'] = $durasiPerkiraan * $data['harga_perhari'];
                            }
                        }

                        $this->getRelationship()->attach($recordId, $data);

                        $alat = DaftarAlat::find($recordId);
                        if ($alat) {
                            $alat->update(['status' => false]);
                        }
                    })
                    ->form(fn(AttachAction $action): array => [
                        Forms\Components\Select::make('jenis_alat_filter')->label('Pilih Jenis Alat')->options(DaftarAlat::pluck('jenis_alat', 'jenis_alat')->unique())->live()->required(),
                        Forms\Components\Select::make('recordId')->label('Pilih Nomor Seri')->options(function (Get $get): array {
                            $jenisAlat = $get('jenis_alat_filter');
                            if (!$jenisAlat)
                                return [];
                            $alreadyAttachedAlatIds = $this->getOwnerRecord()->daftarAlat()->pluck('daftar_alat.id');
                            return DaftarAlat::query()->where('jenis_alat', $jenisAlat)->where('status', true)->where('kondisi', true)->whereNotIn('id', $alreadyAttachedAlatIds)->pluck('nomor_seri', 'id')->all();
                        })->searchable()->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        Forms\Components\DatePicker::make('tgl_keluar')->label('Tanggal Keluar')->default(now())->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        Forms\Components\TextInput::make('harga_perhari')->label('Harga Sewa Per Hari')->numeric()->prefix('Rp')->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('batal')
                    ->visible(function ($record) {
                        $today = today();
                        $createdAt = $record->created_at?->copy()->startOfDay();
                        return ($today == $createdAt);
                    })
                    ->after(function (array $data, Model $record) {
                        $record->update(['status' => true]);
                    }),
                Tables\Actions\Action::make('info')
                    ->label('Tidak bisa dibatalkan')
                    ->disabled()
                    ->color('gray')
                    ->visible(function ($record) {
                        $today = today();
                        $createdAt = $record->created_at?->copy()->startOfDay();
                        return ($today != $createdAt);
                    }),
                Tables\Actions\EditAction::make()
                    ->label('Update Pengembalian')
                    ->using(function (Model $record, array $data): Model {
                        if (!empty($data['tgl_masuk'])) {
                            $tglKeluar = Carbon::parse($data['tgl_keluar']);
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
                            $record->update([
                                'status' => true,
                                'kondisi' => $kondisiBaru,
                            ]);
                        }
                        return $record;
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
