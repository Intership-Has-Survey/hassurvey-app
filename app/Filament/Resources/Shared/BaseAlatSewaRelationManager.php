<?php

namespace App\Filament\Resources\Shared;

use App\Models\DaftarAlat;
use App\Models\JenisAlat;
use App\Models\Merk;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;
use Filament\Forms\Components\FileUpload;

abstract class BaseAlatSewaRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';
    protected static bool $isLazy = false;

    abstract protected function getSewaRecord(): Sewa;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tgl_keluar')->label('Tanggal Alat Keluar')->disabled()->dehydrated(),
                Forms\Components\DatePicker::make('tgl_masuk')->label('Tanggal Masuk')->minDate(fn(Get $get) => $get('tgl_keluar'))->live()->required(),

                // --- PERBAIKAN DI SINI ---
                Forms\Components\TextInput::make('harga_perhari')
                    ->label('Harga Per Hari')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),

                Forms\Components\TextInput::make('diskon_hari')->label('Diskon Hari')->numeric()->nullable()->placeholder('Masukkan diskon berapa hari jika ada')->postfix(' Hari')->minValue(0),
                Forms\Components\Select::make('kondisi_kembali')->label('Kondisi Saat Kembali')->options(['Baik' => 'Baik', 'Bermasalah' => 'Bermasalah'])->required()->default('Baik')->live()->dehydrated(),
                Forms\Components\Toggle::make('needs_replacement')->label('Butuh Alat Pengganti?')->helperText('Aktifkan jika alat ini perlu diganti dengan unit lain.')->visible(fn(Get $get): bool => $get('kondisi_kembali') === 'Bermasalah')->default(false),
                Forms\Components\Textarea::make('catatan')->label('catatan')->columnSpanFull(),
                FileUpload::make('foto_bukti')->label('Foto Bukti')->image()->directory('bukti-pengembalian')->required()->columnSpanFull(),
            ])
            ->disabled(fn(): bool => $this->getSewaRecord()->is_locked);
    }

    public function perhitunganFinal(Model $record, Sewa $sewa)
    {
        $pivotData = $record->pivot;

        // Get all alat related to this sewa with tgl_masuk not null
        $allAlat = $sewa->daftarAlat()->wherePivotNotNull('tgl_masuk')->get();

        // Calculate total biaya_sewa_alat from all alat
        $totalBiayaSewaAlat = $allAlat->sum(function ($alat) {
            return $alat->pivot->biaya_sewa_alat ?? 0;
        });

        // Use harga_fix if sewa is locked, else fallback to harga_real
        $totalHarga = $sewa->is_locked ? $sewa->harga_fix : $sewa->harga_real;

        if ($totalBiayaSewaAlat > 0) {
            // Calculate weight for this record
            $weight = ($pivotData->biaya_sewa_alat ?? 0) / $totalBiayaSewaAlat;


            // Calculate final values using prorata allocation
            $biayaSewaAlatFinal = $weight * $totalHarga;
            if ($record->pemilik && $record->pemilik->persen_bagihasil) {
                $persentasePemilik = $record->pemilik->persen_bagihasil / 100;
                $pendapatanInvFinal = $biayaSewaAlatFinal * $persentasePemilik;
                $pendapatanHasFinal = $biayaSewaAlatFinal - $pendapatanInvFinal;
            }

        } else {
            // If total biaya_sewa_alat is zero, fallback to original values
            $biayaSewaAlatFinal = $pivotData->biaya_sewa_alat ?? 0;
            $pendapatanInvFinal = $pivotData->pendapataninv ?? 0;
            $pendapatanHasFinal = $pivotData->pendapatanhas ?? 0;
        }

        // Save final calculated values to pivot
        $pivotData->biaya_sewa_alat_final = $biayaSewaAlatFinal;
        $pivotData->pendapataninv_final = $pendapatanInvFinal;
        $pivotData->pendapatanhas_final = $pendapatanHasFinal;

        $pivotData->save();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                TextColumn::make('jenisAlat.nama')->label('Jenis Alat')->searchable(),
                TextColumn::make('nomor_seri')->searchable(),
                BadgeColumn::make('kondisi')->label('Kondisi Alat')->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')->color(fn(bool $state) => $state ? 'success' : 'danger'),
                TextColumn::make('tgl_keluar')->date('d-m-Y'),
                TextColumn::make('tgl_masuk')->date('d-m-Y')->placeholder('Belum Kembali'),
                TextColumn::make('harga_perhari')->money('IDR')->sortable(),
                TextColumn::make('biaya_perkiraan_alat')->label('Biaya Perkiraan')
                    ->money('IDR')
                    ->visible(fn(): bool => true)
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
                TextColumn::make('biaya_sewa_alat')->label('Biaya Realisasi')->money('IDR')->placeholder('Belum Kembali')->sortable(),
                TextColumn::make('biaya_sewa_alat_final')->label('Harga Final Alat')->money('IDR')
                    ->visible(fn(): bool => true)
                    ->state(fn(Model $record): ?float => $record->pivot->biaya_sewa_alat_final ?? null),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Alat Sewa')
                    ->visible(fn(): bool => $this->getSewaRecord()->canAddTools() && !$this->getSewaRecord()->is_locked)
                    ->preloadRecordSelect()
                    ->using(function (array $data, RelationManager $livewire): void {
                        $sewa = $livewire->getSewaRecord();
                        $recordId = $data['recordId'];
                        $data['sewa_id'] = $sewa->id;
                        $livewire->getRelationship()->attach($recordId, $data);
                        $alat = DaftarAlat::find($recordId);
                        if ($alat) {
                            $alat->update(['status' => false]);
                        }
                        // If need_replacement is true (1), set it to false (0) when adding a new alat
                        if ($sewa->need_replacement == 1) {
                            $sewa->need_replacement = 0;
                            $sewa->save();
                        }
                    })
                    ->form(function (): array {
                        $sewa = $this->getSewaRecord();
                        $alreadyAttachedAlatIds = $sewa->daftarAlat()->pluck('daftar_alat.id');
                        return [
                            Forms\Components\Select::make('jenis_alat_id_filter')->label('Filter Berdasarkan Jenis Alat')->options(JenisAlat::pluck('nama', 'id'))->live()->dehydrated(false),
                            Forms\Components\Select::make('recordId')->label('Pilih Nomor Seri')->options(function (Get $get) use ($alreadyAttachedAlatIds): array {
                                $jenisAlatId = $get('jenis_alat_id_filter');
                                if (!$jenisAlatId) {
                                    return [];
                                }
                                $query = DaftarAlat::query()->where('status', true)->where('kondisi', true)->whereNotIn('id', $alreadyAttachedAlatIds);
                                if ($jenisAlatId) {
                                    $query->where('jenis_alat_id', $jenisAlatId);
                                }
                                return $query->pluck('nomor_seri', 'id')->all();
                            })->searchable()->required()->visible(fn(Get $get) => filled($get('jenis_alat_id_filter'))),

                            Forms\Components\DatePicker::make('tgl_keluar')
                                ->label('Tanggal Keluar')
                                ->default(now())
                                ->required()
                                ->minDate(now()->subDays(3))
                                //->maxDate(now())
                                ->visible(fn(Get $get) => filled($get('jenis_alat_id_filter'))),

                            // --- PERBAIKAN DI SINI JUGA ---
                            Forms\Components\TextInput::make('harga_perhari')
                                ->label('Harga Sewa Per Hari')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->visible(fn(Get $get) => filled($get('jenis_alat_id_filter'))),
                        ];
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->visible(fn(Model $record): bool => !is_null($record->pivot->tgl_masuk))
                    ->form([
                        Forms\Components\TextInput::make('nomor_seri')->label('Nomor Seri Alat')->disabled(),
                        Forms\Components\TextInput::make('tgl_keluar')->label('Tanggal Keluar')->disabled(),
                        Forms\Components\TextInput::make('tgl_masuk')->label('Tanggal Kembali')->disabled(),
                        Forms\Components\TextInput::make('kondisi_kembali')->label('Kondisi Saat Kembali')->disabled(),
                        Forms\Components\TextInput::make('diskon_hari')->label('Diskon Hari')->numeric()->disabled()->postfix(' Hari'),
                        Forms\Components\TextInput::make('biaya_sewa_alat')->label('Pendapatan Kotor')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Forms\Components\TextInput::make('pendapataninv')->label('Pendapatan Investor')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Forms\Components\TextInput::make('pendapatanhas')->label('Pendapatan Has Survey')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),

                        Forms\Components\Placeholder::make('')->content('Informasi Harga Final')->dehydrated(false),
                        Forms\Components\TextInput::make('biaya_sewa_alat_final')->label('Pendapatan Kotor')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Forms\Components\TextInput::make('pendapataninv_final')->label('Pendapatan Investor')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Forms\Components\TextInput::make('pendapatanhas_final')->label('Pendapatan Has Survey')->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                    ]),
                Tables\Actions\EditAction::make()
                    ->label('Kembalikan')
                    ->visible(fn(Model $record): bool => is_null($record->pivot->tgl_masuk) && !$this->getSewaRecord()->is_locked)
                    ->using(function (Model $record, array $data): Model {
                        if (!empty($data['tgl_masuk'])) {
                            $tglKeluar = Carbon::parse($record->pivot->tgl_keluar);
                            $tglMasuk = Carbon::parse($data['tgl_masuk']);
                            $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                            $diskon = $data['diskon_hari'] ?? 0;
                            $hargaPerHari = (float) $data['harga_perhari'];

                            // 1. Hitung Pendapatan Kotor
                            $pendapatanKotor = ($durasiSewa - $diskon) * $hargaPerHari;

                            // 2. Inisialisasi pendapatan
                            $pendapatanInvestor = 0;
                            $pendapatanHasSurvey = $pendapatanKotor;

                            // 3. Lakukan bagi hasil jika ada pemilik dan persentase
                            if ($record->pemilik && $record->pemilik->persen_bagihasil) {
                                $persentasePemilik = $record->pemilik->persen_bagihasil / 100;
                                $pendapatanInvestor = $pendapatanKotor * $persentasePemilik;
                                $pendapatanHasSurvey = $pendapatanKotor - $pendapatanInvestor;
                            }

                            // 4. Simpan data ke tabel pivot (riwayat_sewa)
                            $record->pivot->tgl_masuk = $data['tgl_masuk'];
                            $record->pivot->harga_perhari = $hargaPerHari;
                            $record->pivot->diskon_hari = $diskon;
                            $record->pivot->biaya_sewa_alat = $pendapatanKotor;
                            $record->pivot->pendapataninv = $pendapatanInvestor;
                            $record->pivot->pendapatanhas = $pendapatanHasSurvey;
                            $record->pivot->catatan = $data['catatan'];
                            $record->pivot->foto_bukti = $data['foto_bukti'];
                            $record->pivot->kondisi_kembali = $data['kondisi_kembali'];
                            $record->pivot->needs_replacement = $data['needs_replacement'] ?? false;
                            $record->pivot->save();
                        }
                        return $record;
                    })
                    ->after(function (Model $record, array $data): void {
                        if (empty($data['tgl_masuk'])) {
                            return;
                        }

                        $alat = DaftarAlat::find($record->id);
                        if ($alat) {
                            $isGoodCondition = ($data['kondisi_kembali'] === 'Baik');
                            $alat->update([
                                'kondisi' => $isGoodCondition,
                                'status' => true, // Always set status to 'Tersedia' upon return
                            ]);
                        }
                    }),
                Tables\Actions\EditAction::make('edit_diskon')
                    ->label('Edit Diskon')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn(Model $record): bool => !is_null($record->pivot->tgl_masuk) && !$this->getSewaRecord()->is_locked)
                    ->form([
                        Forms\Components\TextInput::make('diskon_hari')->label('Diskon Hari')->numeric()->nullable()->postfix(' Hari')->default(0)->minValue(0)->required(),
                    ])
                    ->fillForm(fn(Model $record): array => ['diskon_hari' => $record->pivot->diskon_hari])
                    ->using(function (Model $record, array $data): Model {
                        $tglKeluar = Carbon::parse($record->pivot->tgl_keluar);
                        $tglMasuk = Carbon::parse($record->pivot->tgl_masuk);
                        $hargaPerHari = $record->pivot->harga_perhari;
                        $diskonHariBaru = $data['diskon_hari'] ?? 0;
                        $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;

                        $biayaSewaBaru = ($durasiSewa - $diskonHariBaru) * $hargaPerHari;
                        $pendapatanKotorBaru = $biayaSewaBaru;

                        $persentasePemilik = $record->pemilik->persen_bagi_hasil / 100;
                        $pendapatanInvestorBaru = $pendapatanKotorBaru * $persentasePemilik;
                        $pendapatanHasSurveyBaru = $pendapatanKotorBaru - $pendapatanInvestorBaru;

                        $record->pivot->diskon_hari = $diskonHariBaru;
                        $record->pivot->biaya_sewa_alat = $pendapatanKotorBaru;
                        $record->pivot->pendapataninv = $pendapatanInvestorBaru;
                        $record->pivot->pendapatanhas = $pendapatanHasSurveyBaru;
                        $record->pivot->save();
                        return $record;
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('Batalkan')
                    ->visible(function (Model $record): bool {
                        if ($this->getSewaRecord()->is_locked || !is_null($record->pivot->tgl_masuk)) {
                            return false;
                        }
                        $cancellationDeadline = Carbon::parse($record->pivot->created_at)->addHours(12);
                        return now()->lte($cancellationDeadline);
                    })
                    ->after(function (Model $record) {
                        $record->update(['status' => true]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ])
                    ->visible(fn(): bool => !$this->getSewaRecord()->is_locked && $this->getSewaRecord()->canAddTools()),
            ]);
    }
}
