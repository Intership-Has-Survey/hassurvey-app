<?php

namespace App\Filament\Resources\Shared;

use App\Models\DaftarAlat;
use App\Models\Sewa;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Column;
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
                Forms\Components\DatePicker::make('tgl_masuk')->label('Tanggal Masuk')->minDate(fn(Get $get) => $get('tgl_keluar'))->live()->required(),//->maxDate(now()),
                Forms\Components\TextInput::make('harga_perhari')->numeric()->prefix('Rp')->required(),
                Forms\Components\TextInput::make('diskon_hari')->label('Diskon Hari')->numeric()->nullable()->placeholder('Masukkan diskon berapa hari jika ada')->postfix(' Hari')->minValue('0'),
                Forms\Components\Select::make('kondisi_kembali')->label('Kondisi Saat Kembali')->options(['Baik' => 'Baik', 'Bermasalah' => 'Bermasalah'])->required()->default('Baik')->live(),
                Forms\Components\Toggle::make('needs_replacement')->label('Butuh Alat Pengganti?')->helperText('Aktifkan jika alat ini perlu diganti dengan unit lain.')->visible(fn(Get $get): bool => $get('kondisi_kembali') === 'Bermasalah')->default(false),
                Forms\Components\Textarea::make('keterangan')->label('Keterangan')->columnSpanFull(),
                FileUpload::make('foto_bukti')->label('Foto Bukti')->image()->directory('bukti-pengembalian')->required()->columnSpanFull(),
            ])
            ->disabled(fn(): bool => $this->getSewaRecord()->is_locked);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                TextColumn::make('nomor_seri')->searchable(),
                BadgeColumn::make('kondisi')->label('Kondisi Alat')->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')->color(fn(bool $state) => $state ? 'success' : 'danger'),
                TextColumn::make('tgl_keluar')->date('d-m-Y'),
                TextColumn::make('tgl_masuk')->date('d-m-Y')->placeholder('Belum Kembali'),
                TextColumn::make('harga_perhari')->money('IDR')->sortable(),
                TextColumn::make('biaya_perkiraan_alat')->label('Biaya Perkiraan')->money('IDR')->state(function (Model $record): ?float {
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
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Alat Sewa')
                    ->visible(fn(): bool => $this->getSewaRecord()->canAddTools())
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
                    })
                    ->form(function (): array {
                        $sewa = $this->getSewaRecord();
                        $alreadyAttachedAlatIds = $sewa->daftarAlat()->pluck('daftar_alat.id');
                        return [
                            Forms\Components\Select::make('jenis_alat_filter')->label('Pilih Jenis Alat')->options(DaftarAlat::pluck('jenis_alat', 'jenis_alat')->unique())->live()->required()->dehydrated(false),
                            Forms\Components\Select::make('recordId')->label('Pilih Nomor Seri')->options(function (Get $get) use ($alreadyAttachedAlatIds): array {
                                $jenisAlat = $get('jenis_alat_filter');
                                if (!$jenisAlat)
                                    return [];
                                return DaftarAlat::query()->where('jenis_alat', $jenisAlat)->where('status', true)->where('kondisi', true)->whereNotIn('id', $alreadyAttachedAlatIds)->pluck('nomor_seri', 'id')->all();
                            })->searchable()->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                            Forms\Components\DatePicker::make('tgl_keluar')->label('Tanggal Keluar')->default(now())->required()->minDate(now()->subDays(3))->maxDate(now())->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                            Forms\Components\TextInput::make('harga_perhari')->label('Harga Sewa Per Hari')->numeric()->prefix('Rp')->required()->visible(fn(Get $get) => filled($get('jenis_alat_filter'))),
                        ];
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kembalikan')
                    ->visible(fn(Model $record): bool => is_null($record->pivot->tgl_masuk) && !$this->getSewaRecord()->is_locked)
                    ->using(function (Model $record, array $data): Model {
                        if (!empty($data['tgl_masuk'])) {
                            $tglKeluar = Carbon::parse($record->pivot->tgl_keluar);
                            $tglMasuk = Carbon::parse($data['tgl_masuk']);
                            $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                            $biayaSewa = ($durasiSewa - $data['diskon_hari']) * $data['harga_perhari'];
                            $record->pivot->tgl_masuk = $data['tgl_masuk'];
                            $record->pivot->harga_perhari = $data['harga_perhari'];
                            $record->pivot->biaya_sewa_alat = $biayaSewa;
                            $record->pivot->keterangan = $data['keterangan'];
                            $record->pivot->foto_bukti = $data['foto_bukti'];
                            $record->pivot->needs_replacement = $data['needs_replacement'] ?? false;
                            $record->pivot->save();
                            $kondisiBaru = ($data['kondisi_kembali'] === 'Bermasalah') ? false : true;
                            $record->update(['status' => true, 'kondisi' => $kondisiBaru]);
                        }
                        return $record;
                    }),
                Tables\Actions\ViewAction::make()
                    ->visible(function (): bool {
                        $sewa = $this->getSewaRecord();

                        // Selalu tampilkan jika sewa sudah terkunci
                        if ($sewa->is_locked) {
                            return true;
                        }

                        // Atau, tampilkan jika semua alat sudah dikembalikan
                        $alatBelumKembali = $sewa->daftarAlat()->wherePivotNull('tgl_masuk')->count();
                        if ($alatBelumKembali === 0) {
                            return true;
                        }

                        return false;
                    }),
                Tables\Actions\EditAction::make('edit_diskon')
                    ->label('Edit Diskon')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(function (Model $record): bool {
                        // Tampil hanya jika alat sudah kembali DAN sewa belum terkunci
                        return !is_null($record->pivot->tgl_masuk) && !$this->getSewaRecord()->is_locked;
                    })
                    // HANYA GUNAKAN ->form() DAN ->fillForm()
                    ->form([
                        Forms\Components\TextInput::make('diskon_hari')
                            ->label('Diskon Hari')
                            ->numeric()
                            ->nullable()
                            ->postfix(' Hari')
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ])
                    // fillForm adalah satu-satunya yang kita butuhkan untuk mengisi nilai awal
                    ->fillForm(fn(Model $record): array => [
                        'diskon_hari' => $record->pivot->diskon_hari,
                    ])
                    ->using(function (Model $record, array $data): Model {
                        $tglKeluar = Carbon::parse($record->pivot->tgl_keluar);
                        $tglMasuk = Carbon::parse($record->pivot->tgl_masuk);
                        $hargaPerHari = $record->pivot->harga_perhari;
                        $diskonHariBaru = $data['diskon_hari'] ?? 0;

                        $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                        $biayaSewaBaru = ($durasiSewa - $diskonHariBaru) * $hargaPerHari;

                        // Update pivot record dengan data baru
                        $record->pivot->diskon_hari = $diskonHariBaru;
                        $record->pivot->biaya_sewa_alat = $biayaSewaBaru;
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