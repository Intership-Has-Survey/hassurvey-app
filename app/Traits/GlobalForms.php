<?php

namespace App\Traits;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\TrefRegion;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use App\Models\BankAccount;

trait GlobalForms
{
    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->maxLength(200)
                        ->required(),
                    TextInput::make('nib')
                        ->label('NIB')
                        ->maxLength(20),
                    Select::make('level')
                        ->label('Level Perusahaan')
                        ->options([
                            'kecil' => 'Kecil',
                            'menengah' => 'Menengah',
                            'besar' => 'Besar',
                        ]),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Section::make('Alamat Perusahaan')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getPeroranganForm(): array
    {
        return [
            Section::make('Informasi Perorangan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('NIK')
                        ->numeric()
                        ->maxLength(16)
                        ->unique(ignoreRecord: true),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                    Select::make('gender')
                        ->label('Jenis Kelamin')
                        ->options([
                            'Pria' => 'Pria',
                            'Wanita' => 'Wanita',
                        ]),
                ])->columns(2),

            Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getAddressFields(): array
    {
        return [
            Select::make('provinsi')
                ->label('Provinsi')

                ->placeholder('Pilih provinsi')
                ->options(TrefRegion::query()
                    ->where(DB::raw('LENGTH(code)'), 2)
                    ->pluck('name', 'code'))
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('kota', null);
                    $set('kecamatan', null);
                    $set('desa', null);
                }),

            Select::make('kota')
                ->label('Kota/Kabupaten')

                ->placeholder('Pilih kota/kabupaten')
                ->options(function (Get $get) {
                    $provinceCode = $get('provinsi');
                    if (!$provinceCode)
                        return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $provinceCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 5)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('kecamatan', null);
                    $set('desa', null);
                }),

            Select::make('kecamatan')
                ->label('Kecamatan')

                ->placeholder('Pilih kecamatan')
                ->options(function (Get $get) {
                    $regencyCode = $get('kota');
                    if (!$regencyCode)
                        return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $regencyCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 8)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('desa', null);
                }),

            Select::make('desa')
                ->label('Desa/Kelurahan')

                ->placeholder('Pilih desa/kelurahan')
                ->options(function (Get $get) {
                    $districtCode = $get('kecamatan');
                    if (!$districtCode)
                        return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $districtCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 13)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable(),

            Textarea::make('detail_alamat')

                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    private static function getSalesForm(): array
    {
        return [
            Section::make('Informasi Sales')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Sales')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('NIK')
                        ->maxLength(16)
                        ->unique(ignoreRecord: true)
                        ->numeric(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Section::make('Alamat Sales')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getKategoriForm(): array
    {
        return [
            Section::make('Informasi Kategori')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Kategori')
                        ->nullable()
                        ->required()
                        ->maxLength(100),
                    Textarea::make('keterangan')
                        ->label('Deskripsi')
                        ->maxLength(500)
                        ->nullable(),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getPengajuanDanaForm(): array
    {
        return [
            Section::make('Informasi Pengajuan Dana')
                ->schema([
                    TextInput::make('judul_pengajuan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('deskripsi_pengajuan')
                        ->label('Deskripsi Umum')
                        ->columnSpanFull(),

                    Hidden::make('tipe_pengajuan')
                        ->default('project'),
                    Hidden::make('nilai')
                        ->default('0'),
                    Hidden::make('user_id')
                        ->default(auth()->id()),
                    Select::make('bank_id')
                        ->relationship('bank', 'nama_bank')
                        ->placeholder('Pilih Bank')
                        ->searchable()
                        ->preload()
                        ->label('Daftar Bank')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set) => $set('bank_account_id', null)),
                    Select::make('bank_account_id')
                        ->label('Nomor Rekening')
                        ->options(function (callable $get) {
                            $bankId = $get('bank_id');
                            if (!$bankId) {
                                return [];
                            }

                            return \App\Models\BankAccount::where('bank_id', $bankId)
                                ->get()
                                ->mapWithKeys(function ($account) {
                                    return [$account->id => "{$account->no_rek} ({$account->nama_pemilik})"];
                                });
                        })
                        ->reactive()
                        ->createOptionForm([
                            TextInput::make('no_rek')
                                ->label('Nomor Rekening')
                                ->required(),
                            TextInput::make('nama_pemilik')
                                ->label('Nama Pemilik')
                                ->required(),
                            Hidden::make('bank_id')
                                ->default(fn(callable $get) => $get('bank_id')),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ])
                        ->createOptionUsing(function (array $data, callable $get): string {
                            $data['bank_id'] = $get('bank_id');

                            $account = \App\Models\BankAccount::create($data);
                            return $account->id; // UUID
                        })
                        ->searchable()
                        ->native(false)
                        ->required(),

                    Repeater::make('detailPengajuans')
                        ->relationship()
                        ->columnSpanFull()
                        ->label('Rincian Pengajuan Dana')
                        ->schema([
                            TextInput::make('deskripsi')
                                ->label('Nama Item')
                                ->required(),
                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->numeric()
                                ->required(),

                            TextInput::make('harga_satuan')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->prefix('Rp ')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->createItemButtonLabel('Tambah Rincian')
                        ->columns(3),
                ]),
        ];
    }
}
