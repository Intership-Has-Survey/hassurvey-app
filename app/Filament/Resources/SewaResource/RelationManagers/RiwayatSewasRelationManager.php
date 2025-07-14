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

class RiwayatSewasRelationManager extends RelationManager
{
    protected static string $relationship = 'daftarAlat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tgl_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->readOnly(),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Masuk (diisi saat pengembalian)')
                    ->required()
                    ->minDate(fn(Get $get) => $get('tgl_keluar')),
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
                TextColumn::make('biaya_sewa_alat')->money('IDR')->sortable()->label('Total Biaya'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Alat Sewa')
                    ->preloadRecordSelect()
                    ->after(function (array $data, Model $record) {
                        $record->update(['status' => false]);
                    })
                    ->form(fn(AttachAction $action): array => [
                        Forms\Components\Select::make('jenis_alat_filter')
                            ->label('Pilih Jenis Alat')
                            ->options(DaftarAlat::pluck('jenis_alat', 'jenis_alat')->unique())
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('recordId')
                            ->label('Pilih Nomor Seri')
                            // SOLUSI: Menggunakan $this->getOwnerRecord() untuk mengakses record induk
                            ->options(function (Get $get): array {
                                $jenisAlat = $get('jenis_alat_filter');
                                if (!$jenisAlat)
                                    return [];

                                // Dapatkan ID alat yang sudah terpasang pada sewa ini
                                $alreadyAttachedAlatIds = $this->getOwnerRecord()->daftarAlat()->pluck('daftar_alat.id');

                                return DaftarAlat::query()
                                    ->where('jenis_alat', $jenisAlat)
                                    ->where('status', true)
                                    ->where('kondisi', true)
                                    // Sembunyikan alat yang sudah ada di daftar sewa ini
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
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Pengembalian')
                    ->using(function (Model $record, array $data): Model {
                        $tglKeluar = Carbon::parse($data['tgl_keluar']);
                        $tglMasuk = Carbon::parse($data['tgl_masuk']);
                        $durasiSewa = $tglKeluar->diffInDays($tglMasuk) + 1;
                        $biayaSewa = $durasiSewa * $data['harga_perhari'];

                        $record->pivot->tgl_masuk = $data['tgl_masuk'];
                        $record->pivot->harga_perhari = $data['harga_perhari'];
                        $record->pivot->biaya_sewa_alat = $biayaSewa;
                        $record->pivot->save();

                        $kondisiBaru = ($data['kondisi_kembali'] === 'Bermasalah') ? false : true;
                        $record->update([
                            'status' => true,
                            'kondisi' => $kondisiBaru,
                        ]);

                        return $record;
                    }),
                Tables\Actions\DetachAction::make()
                    ->after(fn(Model $record) => $record->update(['status' => true])),
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
