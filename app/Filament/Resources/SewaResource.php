<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Sewa;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use function Livewire\Volt\placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\SewaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SewaResource\RelationManagers;
use App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\PengajuanDanasRelationManager;

class SewaResource extends Resource
{
    protected static ?string $model = Sewa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Jasa Sewa';

    protected static ?string $navigationLabel = 'Manajemen Sewa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kontrak')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->label('Judul Penyewaan')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_selesai')
                            ->required()
                            ->minDate(fn(Get $get) => $get('tgl_mulai')),

                    ])->columns(2),
                Section::make('Lokasi Proyek')
                    ->schema([
                        Select::make('provinsi')
                            ->label('Provinsi')
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
                            ->columnSpanFull()
                            ->placeholder('cth: Jl. Supriyadi No,12, RT.3/RW.4'),
                    ])->columns(2),

                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_type')
                            ->label('Tipe Customer')
                            ->options([
                                Perorangan::class => 'Perorangan',
                                Corporate::class => 'Corporate',
                            ])
                            ->dehydrated()
                            ->live()
                            ->required()
                            ->placeholder('Pilih tipe customer terlebih dahulu'),

                        Select::make('customer_id')
                            ->label('Pilih Customer')
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
                                        Section::make('')
                                            ->schema([
                                                TextInput::make('nik')->label('NIK')
                                                    // ->integer()
                                                    ->maxLength(16)
                                                    ->required()
                                                    ->unique(Perorangan::class, 'nik', ignoreRecord: true),
                                                TextInput::make('nama')->required()->label('Nama Lengkap (Sesuai KTP)'),
                                                Select::make('gender')->options(['Pria', 'Wanita'])->required()->label('Jenis Kelamin'),
                                                TextInput::make('email')->email()->required()->unique(Perorangan::class, 'Email'),
                                                TextInput::make('telepon')->tel()->required(),
                                            ]),
                                        Section::make('Alamat')
                                            ->schema([
                                                Select::make('provinsi')
                                                    ->label('Provinsi')
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
                                                    ->columnSpanFull(),
                                            ])->columns(2),
                                        FileUpload::make('foto_ktp')->label('Foto KTP')->image()->required(),
                                        FileUpload::make('foto_kk')->label('Foto KK')->image()->required(),
                                    ];
                                }
                                if ($type === Corporate::class) {
                                    return [
                                        TextInput::make('nama')->label('Nama Perusahaan')->required(),
                                        Select::make('level')->options(['Kecil', 'Menengah', 'Besar'])->required(),
                                        TextInput::make('email')->email()->required()->unique(Corporate::class, 'email'),
                                        TextInput::make('telepon')->tel()->required(),
                                        Section::make('Alamat')
                                            ->schema([
                                                Select::make('provinsi')
                                                    ->label('Provinsi')
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
                                                    ->columnSpanFull(),
                                            ])->columns(2),
                                        TextInput::make('nib')->nullable()->label('NIB (Nomor Induk Berusaha)')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RiwayatSewasRelationManager::class,
            PengajuanDanasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSewa::route('/'),
            'create' => Pages\CreateSewa::route('/create'),
            'edit' => Pages\EditSewa::route('/{record}/edit'),
        ];
    }
}
