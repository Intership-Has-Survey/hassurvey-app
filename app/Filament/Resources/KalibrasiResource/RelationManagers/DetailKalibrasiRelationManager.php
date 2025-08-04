<?php

namespace App\Filament\Resources\KalibrasiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Illuminate\Support\Facades\DB;

class DetailKalibrasiRelationManager extends RelationManager
{
    protected static string $relationship = 'alatCustomers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('alat_customer_id')
                    ->label('Pilih Alat')
                    ->relationship('alatCustomer', 'nomor_seri')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\Select::make('jenis_alat_id')
                            ->relationship('jenisAlat', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('merk_id')
                            ->relationship('merk', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('nomor_seri')
                            ->required()
                            ->unique()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('keterangan')
                            ->nullable(),
                        Section::make('Informasi Pemilik Alat')
                            ->schema([
                                Select::make('customer_flow_type')
                                    ->label('Tipe Pemilik')
                                    ->options([
                                        'perorangan' => 'Perorangan',
                                        'corporate' => 'Corporate'
                                    ])
                                    ->live()
                                    ->required()
                                    ->dehydrated(false) // karena ini bukan field database
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('corporate_id', null);
                                        $set('perorangan_id', null);
                                    }),

                                // Jika corporate
                                Select::make('corporate_id')
                                    ->label('Pilih Perusahaan')
                                    ->relationship('corporate', 'nama')
                                    ->createOptionForm(self::getCorporateForm())
                                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                                    ->required(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                                // Jika perorangan
                                Select::make('perorangan_id')
                                    ->label('Pilih Customer')
                                    ->relationship('perorangan', 'nama')
                                    ->searchable()
                                    ->createOptionForm(self::getPeroranganForm())
                                    ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id)
                                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                                    ->required(fn(Get $get) => $get('customer_flow_type') === 'perorangan'),
                            ]),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        return \App\Models\AlatCustomer::create($data)->id;
                    })
                    ->required(),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now())
                    ->visible('edit')
                    ->native(false),
                Forms\Components\DatePicker::make('tgl_stiker_kalibrasi')
                    ->label('Tanggal Stiker Kalibrasi')
                    ->default(now())
                    ->native(false)
                    ->visibleOn('edit'),
                Forms\Components\DatePicker::make('tgl_keluar')
                    ->visibleOn('edit')
                    ->label('Tanggal Keluar')
                    ->default(now())
                    ->native(false),
                Select::make('status')
                    ->visibleOn('edit')
                    ->options([
                        'belum_dikerjakan' => 'Belum dikerjakan',
                        'proses' => 'Dalam proses',
                        'kalibrasi_diluar' => 'Kalibrasi diluar HAS',
                        'sudah_diservis' => 'Sudah diservis',
                        'terkalibrasi' => 'Terkalibrasi'
                    ])
                    ->default('belum_dikerjakan')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                Tables\Columns\TextColumn::make('alatCustomer.nomor_seri')->label('nomor seri'),
                Tables\Columns\TextColumn::make('tgl_masuk'),
                Tables\Columns\TextColumn::make('tgl_stiker_kalibrasi')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_dikerjakan' => 'gray',
                        'dalam_proses' => 'primary',
                        'kalibrasi_diluar' => 'warning',
                        'sudah_diservis' => 'info',
                        'terkalibrasi' => 'success',
                        default => 'primary'
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->required()
                        ->maxLength(200),
                    TextInput::make('nib')
                        ->label('NIB')
                        ->maxLength(20),
                    Forms\Components\Select::make('level')
                        ->label('Level Perusahaan')
                        ->options([
                            'kecil' => 'Kecil',
                            'menengah' => 'Menengah',
                            'besar' => 'Besar',
                        ])
                        ->native(false),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),
            Forms\Components\Section::make('Alamat Perusahaan')
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
                    Forms\Components\Select::make('gender')
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
                ->required()
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
                ->required()
                ->placeholder('Pilih kota/kabupaten')
                ->options(function (Get $get) {
                    $provinceCode = $get('provinsi');
                    if (!$provinceCode) return [];

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
                ->required()
                ->placeholder('Pilih kecamatan')
                ->options(function (Get $get) {
                    $regencyCode = $get('kota');
                    if (!$regencyCode) return [];

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
                ->required()
                ->placeholder('Pilih desa/kelurahan')
                ->options(function (Get $get) {
                    $districtCode = $get('kecamatan');
                    if (!$districtCode) return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $districtCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 13)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable(),

            Textarea::make('detail_alamat')
                ->required()
                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}
