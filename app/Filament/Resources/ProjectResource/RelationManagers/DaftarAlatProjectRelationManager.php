<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\DaftarAlat;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ButtonColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;

class DaftarAlatProjectRelationManager extends RelationManager
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
                    ->disabled() // <-- Dibuat disabled agar tidak bisa diubah saat edit
                    ->dehydrated(), // <-- Pastikan nilainya tetap terkirim meskipun disabled
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Masuk (diisi saat pengembalian)')
                    ->minDate(fn(Get $get) => $get('tgl_keluar'))
                    ->live(), // <-- Reaktif untuk memicu validasi
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

                // SOLUSI: Menambahkan input untuk keterangan dan foto bukti
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan Pengembalian (Opsional)')
                    ->columnSpanFull(),
                FileUpload::make('foto_bukti')
                    ->label('Foto Bukti Pengembalian')
                    ->image()
                    ->directory('bukti-pengembalian')
                    // Foto hanya wajib diisi jika tanggal masuk sudah diisi
                    ->required(fn(Get $get): bool => filled($get('tgl_masuk')))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $sewa = $this->ownerRecord->sewa;
        return $table
            // ->query(function () use ($sewa) {
            //     if (!$sewa) {
            //         return DaftarAlat::query()->whereNull('id'); // kosong
            //     }

            //     return $sewa->daftarAlat()
            //         ->select('daftar_alat.*', 'riwayat_sewa.sewa_id', 'riwayat_sewa.daftar_alat_id', 'riwayat_sewa.tgl_keluar', 'riwayat_sewa.tgl_masuk', 'riwayat_sewa.harga_perhari', 'riwayat_sewa.biaya_sewa_alat');
            // })
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
                TextColumn::make('biaya_perkiraan_alat')->money('IDR')->sortable()->label('Total Biaya'),
                TextColumn::make('biaya_sewa_alat')->money('IDR')->sortable()->label('Total Biaya'),
            ])
            ->recordTitleAttribute('nomor_seri')
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->using(function (RelationManager $livewire, Model $record, array $data): void {
                        $project = $livewire->ownerRecord;

                        // 1. Cek apakah Project sudah punya Sewa
                        if (!$project->sewa_id) {
                            // Jika belum, buat Sewa baru
                            $sewa = \App\Models\Sewa::create([
                                'judul' => $project->nama_project,
                                'tgl_mulai' => now(),
                                'tgl_selesai' => now()->addDays(7),
                                'provinsi' => $project->provinsi,
                                'kota' => $project->kota,
                                'kecamatan' => $project->kecamatan,
                                'desa' => $project->desa,
                                'detail_alamat' => $project->detail_alamat,
                                'user_id' => auth()->id(),
                                'customer_id' => $project->customer_id,
                                'customer_type' => $project->customer_type,
                            ]);

                            // Update project dengan ID sewa baru
                            $project->update(['sewa_id' => $sewa->id]);
                        } else {
                            // Ambil Sewa yang sudah ada
                            $sewa = $project->sewa;
                        }

                        // 2. Attach alat ke Sewa
                        $pivotData = collect($data)->only([
                            'tgl_keluar',
                            'harga_perhari',
                            'user_id',
                        ])->toArray();

                        $pivotData['sewa_id'] = $sewa->id;

                        $livewire->getRelationship()->attach($record, $pivotData);

                        // 3. Update status alat (false = sedang dipinjam)
                        $record->update(['status' => false]);
                    })
                    ->label('Tambah Alat')
                    ->modalHeading('Tambah Alat ke Proyek')
                    ->preloadRecordSelect()
                    // ->using(function (RelationManager $livewire, Model $record, array $data): void {
                    //     $project = $livewire->ownerRecord;
                    //     $pivotData = collect($data)->only([
                    //         'tgl_keluar',
                    //         'harga_perhari',
                    //         'user_id',
                    //     ])->toArray();
                    //     $pivotData['sewa_id'] = $project->sewa_id;
                    //     $livewire->getRelationship()->attach($record, $pivotData);
                    //     $record->update(['status' => false]);
                    // })
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Pengembalian')
                    ->using(function (Model $record, array $data): Model {
                        // Hanya proses jika tanggal masuk diisi
                        if (!empty($data['tgl_masuk'])) {
                            $tglKeluar = Carbon::parse($data['tgl_keluar']);
                            $tglMasuk = Carbon::parse($data['tgl_masuk']);
                            $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                            $biayaSewa = $durasiSewa * $data['harga_perhari'];

                            // Update data di pivot table
                            $record->pivot->tgl_masuk = $data['tgl_masuk'];
                            $record->pivot->harga_perhari = $data['harga_perhari'];
                            $record->pivot->biaya_sewa_alat = $biayaSewa;
                            // SOLUSI: Menyimpan data keterangan dan foto bukti
                            $record->pivot->keterangan = $data['keterangan'];
                            $record->pivot->foto_bukti = $data['foto_bukti'];
                            $record->pivot->save();

                            // Update data di tabel master daftar_alat
                            $kondisiBaru = ($data['kondisi_kembali'] === 'Bermasalah') ? false : true;
                            $record->update([
                                'status' => true,
                                'kondisi' => $kondisiBaru,
                            ]);
                        }
                        return $record;
                    }),
                Tables\Actions\Action::make('goToSewa')
                    ->label('Update Pengembalian')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('warning')
                    ->url(fn($record, RelationManager $livewire) => route('filament.admin.resources.sewas.edit', [
                        'record' => $livewire->ownerRecord->sewa_id
                    ]))
                    ->openUrlInNewTab() // atau hapus jika ingin redirect di tab yang sama
                    ->visible(fn($record) => is_null($record->tgl_masuk)),
                Tables\Actions\Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn($record) => is_null($record->pivot->tgl_masuk))
                    ->action(function ($record) {
                        $record->pivot->update([
                            'tgl_masuk' => now(),
                        ]);
                    }), // hanya tampil jika belum dikembalikan
            ])



            ->bulkActions([]);
    }
}
