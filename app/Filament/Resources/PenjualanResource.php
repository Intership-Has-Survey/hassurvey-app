<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Sales;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Penjualan;
use App\Models\DaftarAlat;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Models\StatusPembayaran;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Filament\Resources\PenjualanResource\Pages\EditPenjualan;
use App\Filament\Resources\PenjualanResource\Pages\ListPenjualans;
use App\Filament\Resources\PenjualanResource\Pages\CreatePenjualan;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nomor_invoice')
                    ->label('Nama Penjualan')
                    ->required()
                    ->unique(ignoreRecord: true),
                DatePicker::make('tanggal_penjualan')
                    ->required()
                    ->default(now())
                    ->label('Tanggal Penjualan')
                    ->displayFormat('d/m/Y')
                    ->native(false),
                Select::make('customer_flow_type')
                    ->label('Tipe Customer')
                    ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                    ->live()->dehydrated(false)->native(false)
                    ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null))
                    ->columnSpanFull(),
                Select::make('corporate_id')
                    ->label('Pilih Perusahaan')
                    ->options(
                        Corporate::whereNotNull('nama')->pluck('nama', 'id')
                    )
                    ->live()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(self::getCorporateForm())
                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                    ->columnSpanFull(),

                Repeater::make('perorangan')
                    ->label(fn(Get $get): string => $get('customer_flow_type') === 'corporate' ? 'PIC' : 'Pilih Customer')
                    ->relationship()
                    ->schema([
                        Select::make('perorangan_id')
                            ->label(false)
                            ->options(function (Get $get, $state): array {
                                $selectedPicIds = collect($get('../../perorangan'))->pluck('perorangan_id')->filter()->all();
                                $selectedPicIds = array_diff($selectedPicIds, [$state]);
                                return Perorangan::whereNotIn('id', $selectedPicIds)->get()->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])->all();
                            })
                            ->searchable()
                            ->createOptionForm(self::getPeroranganForm())
                            ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id),
                    ])
                    ->minItems(1)
                    ->distinct()
                    ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                    ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                    ->addActionLabel('Tambah PIC')
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                    ->saveRelationshipsUsing(function (Model $record, array $state): void {
                        $ids = array_map(fn($item) => $item['perorangan_id'], $state);
                        $record->perorangan()->sync($ids);

                        if ($record->corporate_id) {
                            $corporate = $record->corporate;
                            foreach ($ids as $peroranganId) {
                                if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
                                    $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                                }
                            }
                        }
                    }),
                Select::make('sales_id')
                    ->label('Sales')
                    ->options(function () {
                        return Sales::query()
                            ->select('id', 'nama', 'nik')
                            ->get()
                            ->mapWithKeys(fn($sales) => [$sales->id => "{$sales->nama} - {$sales->nik}"]);
                    })->searchable()
                    ->required()
                    ->preload()
                    ->columnSpanFull()
                    ->createOptionForm(self::getSalesForm()),

                Repeater::make('detailPenjualan')
                    ->relationship()
                    ->schema([
                        Select::make('jenis_alat_id')
                            ->label('Jenis Alat')
                            ->options(\App\Models\JenisAlat::all()->pluck('nama', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->reactive()
                            ->afterStateHydrated(function ($state, \Filament\Forms\Set $set) {
                                if ($state !== null) {
                                    $set('jenis_alat_id', $state);
                                }
                            }),
                        Select::make('nomor_seri')
                            ->label('Nomor Seri')
                            ->options(function (Get $get) {
                                $jenisAlatId = $get('jenis_alat_id');
                                if (!$jenisAlatId) {
                                    return [];
                                }
                                return \App\Models\DaftarAlat::where('jenis_alat_id', $jenisAlatId)->pluck('nomor_seri', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->live()
                            ->reactive()
                            ->afterStateHydrated(function ($state, \Filament\Forms\Set $set) {
                                if ($state !== null) {
                                    $set('nomor_seri', $state);
                                }
                            })
                            ->afterStateUpdated(function (callable $set, $state) {
                                $daftarAlat = \App\Models\DaftarAlat::find($state);
                                $merkNama = $daftarAlat ? $daftarAlat->merk->nama : '';
                                $set('merk_nama', $merkNama);
                                $set('merk_id', $daftarAlat ? $daftarAlat->merk_id : null);
                                $set('daftar_alat_id', $state);
                            }),

                        Select::make('merk_id')
                            ->label('Merek Alat')
                            ->hidden()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('merk_nama')
                            ->label('Merek Alat')
                            ->disabled()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, callable $get) {
                                // no action needed here, just to trigger reactive update
                            })
                            ->dehydrated(false)
                            ->default(''),

                        TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->required(),

                        Hidden::make('daftar_alat_id')
                            ->required(),
                        Hidden::make('merk_id')
                            ->required(),

                    ])->columns(4)
                    ->columnSpanFull(),

                Textarea::make('catatan'),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_invoice')->searchable()
                    ->label('Nama Penjualan')
                    ->sortable(),
                TextColumn::make('tanggal_penjualan')->date(),
                TextColumn::make('customer_display')
                    ->label('Klien')
                    ->state(function (Penjualan $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        return $record->perorangan->first()?->nama ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2),
                TextColumn::make('sales.nama')->label('Sales'),
                TextColumn::make('status_pembayaran')->label('Pembayaran')->badge()->color(fn(string $state): string => match ($state) {
                    'Lunas' => 'success',
                    'Belum Lunas' => 'danger',
                    default => 'info',
                }),
                TextColumn::make('total_items')
                    ->label('Total Item')
                    ->state(function (\App\Models\Penjualan $record): string {
                        return 'Rp ' . number_format($record->detailPenjualan->sum('harga'), 0, ',', '.');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            RelationManagers\DetailPenjualanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
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

            Forms\Components\Textarea::make('detail_alamat')

                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    private static function getSalesForm(): array
    {
        return [
            Forms\Components\Section::make('Informasi Sales')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Sales')

                        ->maxLength(100),
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')

                        ->length(16)
                        ->unique(ignoreRecord: true)
                        ->numeric(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')

                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('telepon')
                        ->label('Telepon')

                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Forms\Components\Section::make('Alamat Sales')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getCorporateForm(): array
    {
        return [
            Forms\Components\Section::make('Informasi Perusahaan')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->maxLength(200),
                    Forms\Components\TextInput::make('nib')
                        ->label('NIB')
                        ->maxLength(20),
                    Forms\Components\Select::make('level')
                        ->label('Level Perusahaan')
                        ->options([
                            'kecil' => 'Kecil',
                            'menengah' => 'Menengah',
                            'besar' => 'Besar',
                        ]),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('telepon')
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
            Forms\Components\Section::make('Informasi Personal')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Lengkap')

                        ->maxLength(100),
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->length(16)
                        ->numeric()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('telepon')
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

            Forms\Components\Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }
}
