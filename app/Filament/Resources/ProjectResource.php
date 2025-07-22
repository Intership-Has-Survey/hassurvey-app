<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sales;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Layanan Pemetaan';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $title = 'Proyek Pemetaan';
    protected static ?string $modelLabel = 'Project Pemetaan';
    protected static ?string $pluralModelLabel = 'Project Pemetaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Proyek')
                ->description('Informasi dasar mengenai proyek pemetaan')
                ->icon('heroicon-o-information-circle')
                ->disabled(fn(?Model $record) => $record?->status_pekerjaan === 'Selesai')
                ->schema([
                    TextInput::make('nama_project')
                        ->label('Nama Proyek')
                        ->placeholder('Masukkan nama proyek')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('kategori_id')
                        ->relationship('kategori', 'nama')
                        ->placeholder('Pilih kategori proyek')
                        ->searchable()
                        ->preload()
                        ->label('Kategori Proyek')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Kategori')
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
                        ->label('Sales')
                        ->options(function () {
                            return Sales::query()
                                ->select('id', 'nama', 'nik')
                                ->get()
                                ->mapWithKeys(fn($sales) => [$sales->id => "{$sales->nama} - {$sales->nik}"]);
                        })
                        ->placeholder('Pilih sales')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm(self::getSalesForm()),

                    DatePicker::make('tanggal_informasi_masuk')
                        ->required()
                        ->label('Tanggal Informasi Masuk')
                        ->placeholder('Pilih tanggal')
                        ->native(false)
                        ->default(now()),

                    Select::make('sumber')
                        ->options([
                            'Online' => 'Online',
                            'Offline' => 'Offline',
                        ])
                        ->label('Sumber Pemesanan')
                        ->placeholder('Pilih sumber')
                        ->required()
                        ->native(false),
                ])->columns(2),

            Section::make('Lokasi Proyek')
                ->description('Alamat lengkap lokasi pelaksanaan proyek')
                ->icon('heroicon-o-map-pin')
                ->disabled(fn(?Model $record) => $record?->status_pekerjaan === 'Selesai')
                ->schema(self::getAddressFields())
                ->columns(2),

            Section::make('Informasi Customer')
                ->description('Data customer dan person in charge')
                ->icon('heroicon-o-users')
                ->disabled(fn(?Model $record) => $record?->status_pekerjaan === 'Selesai')
                ->schema([
                    Forms\Components\Select::make('customer_flow_type')
                        ->label('Tipe Customer')
                        ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                        ->live()->required()->dehydrated(false)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('corporate_id')
                                ->relationship('corporate', 'nama')
                                ->label('Pilih Perusahaan')
                                ->live()
                                ->createOptionForm([ /* ... */])
                                ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                                ->columnSpanFull(),

                            Forms\Components\Select::make('perorangan_id')
                                ->label('Pilih Customer')
                                ->options(Perorangan::pluck('nama', 'id'))
                                ->searchable()
                                ->required()
                                ->createOptionForm(self::getPeroranganForm())
                                ->createOptionUsing(function (array $data): string {
                                    $data['user_id'] = auth()->id();
                                    $perorangan = Perorangan::create($data);
                                    return $perorangan->id;
                                })
                                ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                                ->columnSpanFull(),

                            Forms\Components\Repeater::make('perorangan')
                                ->label('PIC (Person in Charge)')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('perorangan_id')
                                        ->label('Pilih PIC')
                                        ->options(function (Get $get, $state): array {
                                            $selectedPicIds = collect($get('../../perorangan'))
                                                ->pluck('perorangan_id')
                                                ->filter()
                                                ->all();

                                            $selectedPicIds = array_diff($selectedPicIds, [$state]);

                                            return Perorangan::whereNotIn('id', $selectedPicIds)
                                                ->pluck('nama', 'id')
                                                ->all();
                                        })
                                        ->searchable()
                                        ->required()
                                        ->createOptionForm(self::getPeroranganForm())
                                        ->createOptionUsing(function (array $data, Get $get): string {
                                            $data['user_id'] = auth()->id();
                                            $perorangan = Perorangan::create($data);

                                            $corporateId = $get('../../corporate_id');
                                            if ($corporateId) {
                                                $corporate = Corporate::find($corporateId);
                                                if ($corporate) {
                                                    $corporate->perorangan()->attach($perorangan->id);
                                                }
                                            }
                                            return $perorangan->id;
                                        }),
                                ])
                                ->minItems(1)
                                ->addable()
                                ->grid(2)
                                ->addActionLabel('Tambah PIC')
                                ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                                ->columnSpanFull()
                                ->saveRelationshipsUsing(function (Model $record, array $state): void {
                                    // 1. Ekstrak semua ID perorangan dari state repeater
                                    $ids = array_map(fn($item) => $item['perorangan_id'], $state);
                                    // 2. Lakukan sinkronisasi ke tabel pivot
                                    $record->perorangan()->sync($ids);
                                })
                                ->afterStateUpdated(function (Get $get, $state) {
                                    $flowType = $get('../../customer_flow_type');
                                    $corporateId = $get('../../corporate_id');
                                    if ($flowType === 'corporate' && $corporateId && $state) {
                                        $corporate = Corporate::find($corporateId);
                                        if ($corporate) {
                                            // Menghubungkan PIC yang dipilih ke Corporate
                                            $corporate->perorangan()->syncWithoutDetaching($state);
                                        }
                                    }
                                }),

                        ]),
                ]),

            Section::make('Keuangan & Status')
                ->description('Nilai proyek dan status penanganan')
                ->icon('heroicon-o-currency-dollar')
                ->disabled(fn(?Model $record) => $record?->status_pekerjaan === 'Selesai')
                ->schema([
                    TextInput::make('nilai_project')
                        ->label('Nilai Proyek')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('0')
                        ->required()
                        ->disabled(
                            fn(?Model $record, Get $get) =>
                            $record?->status === 'Closing' || $get('status') === 'Closing'
                        )
                        ->dehydrated(true)
                        ->helperText('Nilai akan terkunci saat status menjadi Closing'),

                    Select::make('status')
                        ->label('Status Proyek')
                        ->options([
                            'Prospect' => 'Prospect',
                            'Follow up 1' => 'Follow up 1',
                            'Follow up 2' => 'Follow up 2',
                            'Follow up 3' => 'Follow up 3',
                            'Closing' => 'Closing'
                        ])
                        ->required()
                        ->native(false)
                        ->live()
                        ->helperText('Status menentukan tahapan penanganan proyek'),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')
                    ->label('Nama Proyek')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('customer_display')
                    ->label('Customer')
                    ->state(function (Project $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        $firstPeorangan = $record->perorangan->first();
                        return $firstPeorangan ? $firstPeorangan->nama : '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    })
                    ->wrap(),

                TextColumn::make('sales.nama')
                    ->label('Sales')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('nilai_project')
                    ->label('Nilai')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Prospect' => 'gray',
                        'Follow up 1' => 'warning',
                        'Follow up 2' => 'warning',
                        'Follow up 3' => 'danger',
                        'Closing' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('status_pembayaran')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        'Sebagian' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('status_pekerjaan')
                    ->label('Pekerjaan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Dalam Progres' => 'warning',
                        'Belum Dimulai' => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('tanggal_informasi_masuk')
                    ->label('Tgl. Masuk')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sumber')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Prospect' => 'Prospect',
                        'Follow up 1' => 'Follow up 1',
                        'Follow up 2' => 'Follow up 2',
                        'Follow up 3' => 'Follow up 3',
                        'Closing' => 'Closing'
                    ]),
                SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status_pembayaran')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                        'Sebagian' => 'Sebagian'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_informasi_masuk', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
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

    // Helper Methods
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
                        ->required()
                        ->length(16)
                        ->unique(ignoreRecord: true)
                        ->numeric(),
                    TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->required()
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

    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->required()
                        ->maxLength(200),
                    TextInput::make('npwp')
                        ->label('NPWP')
                        ->maxLength(20),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getPeroranganForm(): array
    {
        return [
            Section::make('Informasi Personal')
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
                ])->columns(2),

            Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getPeroranganOptions(Get $get): array
    {
        $flowType = $get('../../customer_flow_type');
        $corporateId = $get('../../corporate_id');

        if ($flowType === 'corporate' && $corporateId) {
            $corporate = Corporate::find($corporateId);
            // return $corporate ?
            //     $corporate->perorangan()->pluck('nama', 'id')->all() :
            //     [];
            return Perorangan::pluck('nama', 'id')->all();
        }

        if ($flowType === 'perorangan') {
            return Perorangan::pluck('nama', 'id')->all();
        }

        return [];
    }

    private static function createPeroranganOption(array $data, Get $get): string
    {
        $data['user_id'] = auth()->id();
        $perorangan = Perorangan::create($data);

        $flowType = $get('../../customer_flow_type');
        $corporateId = $get('../../corporate_id');

        if ($flowType === 'corporate' && $corporateId) {
            $corporate = Corporate::find($corporateId);
            if ($corporate) {
                $corporate->perorangan()->attach($perorangan->id);
            }
        }

        return $perorangan->id;
    }
}
