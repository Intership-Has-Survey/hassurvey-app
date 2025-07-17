<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Support\RawJs;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPembayaranRelationManager;
use App\Models\Perorangan;
use App\Models\Corporate;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Project';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Proyek')
                ->schema([
                    TextInput::make('nama_project')
                        ->label('Nama Proyek')
                        ->placeholder('Masukkan nama Proyek')
                        ->required()
                        ->columnSpanFull(),
                    Select::make('kategori_id')
                        ->relationship('kategori', 'nama')
                        ->placeholder('Pilih Kategori Proyek')
                        ->searchable()
                        ->preload()
                        ->label('Kategori Proyek')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Jenis Kategori')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('keterangan')
                                ->label('Keterangan')
                                ->required()
                                ->maxLength(300),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),

                    Select::make('sales_id')
                        ->relationship('sales', 'nama')
                        ->placeholder('Pilih Sales')
                        ->searchable()
                        ->preload()
                        ->label('Sales')
                        ->required()
                        ->createOptionForm([
                            // The address fields were incorrectly here.
                            // They are now moved to the main project section.
                            Section::make('Informasi Sales')
                                ->schema([
                                    TextInput::make('nama')->label('Nama Sales')->required(),
                                    TextInput::make('telepon')->tel()->required(),
                                    TextInput::make('email')->email()->required(),

                                ])->columns(2),
                            Section::make('Alamat Sales')
                                ->schema([
                                    Select::make('provinsi')
                                        ->label('Provinsi')
                                        ->required()
                                        ->placeholder('Pilih Provinsi')
                                        ->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))
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
                                        ->placeholder('Pilih Kota/Kabupaten')
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
                                        ->required()
                                        ->placeholder('Pilih Kecamatan')
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
                                        ->required()
                                        ->placeholder('Pilih Desa/Kelurahan')
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
                                        ->label('Detail Alamat')
                                        ->required()
                                        ->placeholder('Masukkan detail alamat')
                                        ->columnSpanFull(),
                                ])->columns(2),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),

                    // --- ADDRESS FIELDS MOVED HERE ---
                    // These fields now correctly belong to the Project Information.
                    Section::make('Lokasi Proyek')
                        ->schema([
                            Select::make('provinsi')
                                ->label('Provinsi')
                                ->required()
                                ->placeholder('Pilih Provinsi')
                                ->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))
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
                                ->placeholder('Pilih Kota/Kabupaten')
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
                                ->required()
                                ->placeholder('Pilih Kecamatan')
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
                                ->required()
                                ->placeholder('Pilih Desa/Kelurahan')
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
                                ->required()
                                ->placeholder('Masukkan detail alamat')
                                ->label('Detail Alamat')
                                ->columnSpanFull(),
                        ])->columns(2),
                    // --- END OF MOVED ADDRESS FIELDS ---

                    DatePicker::make('tanggal_informasi_masuk')
                        ->required()
                        ->label('Tanggal Informasi Masuk')
                        ->placeholder('Pilih tanggal informasi masuk')
                        ->native(false),
                    Select::make('sumber')
                        ->options(['Online' => 'Online', 'Offline' => 'Offline'])
                        ->label('Sumber Pemesanan')
                        ->placeholder('Pilih jenis sumber pemesanan')
                        ->required()
                        ->native(false),
                ])->columns(2),

            Section::make('Informasi Customer')
                ->schema([
                    Select::make('customer_type')
                        ->label('Tipe Customer')
                        ->options([
                            Perorangan::class => 'Perorangan',
                            Corporate::class => 'Corporate',
                        ])
                        ->live()
                        ->required()
                        ->placeholder('Pilih tipe customer terlebih dahulu'),

                    Select::make('customer_id')
                        ->label('Pilih Customer')
                        ->placeholder('Pilih Nama Customer')
                        ->options(function (Get $get): array {
                            $type = $get('customer_type');
                            if (!$type)
                                return [];
                            return $type::pluck('nama', 'id')->all();
                        })
                        ->searchable()
                        ->required()
                        ->createOptionForm(function (Get $get) {
                            $type = $get('customer_type');
                            if ($type === Perorangan::class) {
                                return [
                                    TextInput::make('nama')->required()->label('Nama Lengkap (Sesuai KTP)'),
                                    Select::make('gender')->options(['Pria' => 'Pria', 'Wanita' => 'Wanita'])->required()->label('Jenis Kelamin'),
                                    TextInput::make('email')->email()->required()->unique(Perorangan::class, 'email'),
                                    TextInput::make('telepon')->tel()->required(),
                                    Select::make('provinsi')
                                        ->label('Provinsi')
                                        ->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))
                                        ->live()
                                        ->searchable()
                                        ->required()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('kota', null);
                                            $set('kecamatan', null);
                                            $set('desa', null);
                                        }),

                                    Select::make('kota')
                                        ->label('Kota/Kabupaten')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $provinsi = $get('provinsi');
                                            if (!$provinsi) {
                                                return [];
                                            }
                                            return TrefRegion::query()
                                                ->where('code', 'like', $provinsi . '.%')
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
                                        ->options(function (Get $get) {
                                            $kota = $get('kota');
                                            if (!$kota) {
                                                return [];
                                            }
                                            return TrefRegion::query()
                                                ->where('code', 'like', $kota . '.%')
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
                                        ->options(function (Get $get) {
                                            $kecamatan = $get('kecamatan');
                                            if (!$kecamatan) {
                                                return [];
                                            }
                                            return TrefRegion::query()
                                                ->where('code', 'like', $kecamatan . '.%')
                                                ->where(DB::raw('LENGTH(code)'), 13)
                                                ->pluck('name', 'code');
                                        })
                                        ->live()
                                        ->searchable(),

                                    Textarea::make('detail_alamat')
                                        ->label('Detail Alamat')
                                        ->required()
                                        ->placeholder('Contoh: Jln. Merdeka No. 123, RT 01/RW 02')
                                        ->columnSpanFull(),

                                    // TextInput::make('alamat')->required()->columnSpanFull(),
                                ];
                            }
                            if ($type === Corporate::class) {
                                return [
                                    TextInput::make('nama')->label('Nama Perusahaan')->required(),
                                    Select::make('level')->options(['Kecil' => 'Kecil', 'Menengah' => 'Menengah', 'Besar' => 'Besar'])->required(),
                                    TextInput::make('email')->email()->required()->unique(Corporate::class, 'email'),
                                    TextInput::make('telepon')->tel()->required(),
                                    Section::make('Alamat Perusahaan')
                                        ->schema([
                                            Select::make('provinsi')->label('Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null)),
                                            Select::make('kota')->label('Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null)),
                                            Select::make('kecamatan')->label('Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                                            Select::make('desa')->label('Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                                            Textarea::make('detail_alamat')->label('Detail Alamat')->columnSpanFull(),
                                        ])->columns(2),
                                    TextInput::make('nib')->nullable()->label('NIB (Nomor Induk Berusaha)')->maxLength(16)->minLength(15),
                                ];
                            }
                            return [];
                        })
                        ->createOptionUsing(function (array $data, Get $get): ?string {
                            $type = $get('customer_type');
                            if (!$type)
                                return null;

                            $data['user_id'] = auth()->id();

                            $record = $type::create($data);
                            return $record->id;
                        })
                        ->visible(fn(Get $get) => filled($get('customer_type'))),
                ]),

            Section::make('Keuangan & Status')
                ->schema([
                    TextInput::make('nilai_project')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->disabled(fn(callable $get) => $get('status') === 'Closing'),
                    Select::make('status')
                        ->label('Status Prospek')
                        ->options(['Prospect' => 'Prospect', 'Follow up' => 'Follow up', 'Closing' => 'Closing'])
                        ->required()
                        ->native(false),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')->sortable()->searchable(),
                TextColumn::make('kategori.nama')->sortable()->searchable(),

                TextColumn::make('customer.nama')
                    ->label('Nama Klien/Perusahaan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')->sortable()->badge(),

                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('status_pekerjaan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Belum Selesai' => 'warning',
                    }),

                TextColumn::make('tanggal_informasi_masuk')->label('Masuk')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PersonelsRelationManager::class,
            RelationManagers\StatusPembayaranRelationManager::class,
            RelationManagers\DaftarAlatProjectRelationManager::class,
            RelationManagers\StatusPekerjaanRelationManager::class,
            RelationManagers\PengajuanDanasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
