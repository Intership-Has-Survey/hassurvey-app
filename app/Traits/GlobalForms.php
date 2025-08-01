<?php

namespace App\Traits;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\TrefRegion;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

trait GlobalForms
{
    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->maxLength(200),
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

                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('NIK')
                        ->length(16)
                        ->numeric()
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

    private static function getKeuanganFields(): array
    {
        return [
            TextInput::make('nilai_project')
                ->label('Anggaran Proyek')
                ->numeric()

                ->prefix('Rp ')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->placeholder('Masukkan anggaran proyek')
                ->disabled(fn(callable $get) => $get('status') === 'Closing'),
            Select::make('status')
                ->label('Status Proyek')
                ->options([
                    'Prospect' => 'Prospect',
                    'Follow up 1' => 'Follow up 1',
                    'Follow up 2' => 'Follow up 2',
                    'Follow up 3' => 'Follow up 3',
                    'Closing' => 'Closing',
                    'Failed' => 'Failed',
                ])

                ->native(false),
        ];
    }

    private static function getSalesForm(): array
    {
        return [
            Section::make('Informasi Sales')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Sales')

                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('NIK')

                        ->length(16)
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
}
