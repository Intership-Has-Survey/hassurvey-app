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
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;

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
                        ->required()
                        ->columnSpanFull(),
                    Select::make('kategori_id')
                        ->relationship('kategori', 'nama')
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
                        ->searchable()
                        ->preload()
                        ->label('Sales')
                        ->required()
                        ->createOptionForm([
                            Section::make('informasi Sales')
                                ->schema([
                                    TextInput::make('nama')->label('Nama Sales')->required(),
                                    TextInput::make('telepon')->tel()->required(),
                                    TextInput::make('email')->email()->required(),
                                ])->columns(2),
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
                                        ->label('Detail Alamat')
                                        ->columnSpanFull(),
                                ])->columns(2),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),

                    DatePicker::make('tanggal_informasi_masuk')
                        ->required()
                        ->native(false),
                    Select::make('sumber')
                        ->options(['Online' => 'Online', 'Offline' => 'Offline'])
                        ->required()
                        ->native(false),
                ])->columns(2),

            Section::make('Informasi Customer')
                ->schema([
                    Select::make('customer_id')
                        ->relationship('customer', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Nama Klien/Perusahaan')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Klien/Perusahaan')
                                ->required(),
                            TextInput::make('email')->email(),
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
                                        ->label('Detail Alamat')
                                        ->columnSpanFull(),
                                ])->columns(2),
                            Hidden::make('user_id')->default(auth()->id()),
                        ])
                        ->columnSpanFull(),
                    Select::make('jenis_penjualan')
                        ->options([
                            'Perseorangan' => 'Perseorangan',
                            'Corporate' => 'Corporate',
                        ])
                        ->required()
                        ->native(false)
                        ->live(),
                    TextInput::make('nama_pic')
                        ->label('Nama PIC')
                        ->visible(fn(Get $get) => $get('jenis_penjualan') === 'Corporate'),
                    Select::make('level_company')
                        ->label('Level Perusahaan')
                        ->options(['Besar' => 'Besar', 'Kecil' => 'Kecil'])
                        ->visible(fn(Get $get) => $get('jenis_penjualan') === 'Corporate')
                        ->native(false),
                    TextInput::make('lokasi')
                        ->label('Lokasi Proyek')
                        ->required(),
                    Textinput::make('alamat')
                        ->required()
                        ->columnSpanFull(),
                ]),

            // --- BAGIAN KEUANGAN & STATUS ---
            Section::make('Keuangan & Status')
                ->schema([
                    TextInput::make('nilai_project')
                        ->label('Nilai Project')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Select::make('status')
                        ->label('Status Prospek')
                        ->options(['Prospect' => 'Prospect', 'Follow up' => 'Follow up', 'Closing' => 'Closing'])
                        ->required()
                        ->native(false),
                    TextInput::make('status_pembayaran')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('status_pekerjaan')
                        ->disabled()
                        ->dehydrated(false),
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
