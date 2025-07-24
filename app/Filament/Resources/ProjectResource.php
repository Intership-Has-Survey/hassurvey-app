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
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Layanan Pemetaan';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Proyek')
                ->schema([
                    TextInput::make('nama_project')->required()->columnSpanFull(),
                    Select::make('kategori_id')->relationship('kategori', 'nama')->searchable()->preload()->required()
                        ->createOptionForm(self::getKategoriForm()),
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
                    DatePicker::make('tanggal_informasi_masuk')->required()->native(false)->default(now()),
                    Select::make('sumber')->options(['Online' => 'Online', 'Offline' => 'Offline'])->required()->native(false),
                ])->columns(2),

            Section::make('Informasi Customer')
                ->schema([
                    Select::make('customer_flow_type')
                        ->label('Tipe Customer')
                        ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                        ->live()->required()->dehydrated(false)
                        ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null)),


                    Select::make('corporate_id')
                        ->relationship('corporate', 'nama')
                        ->label('Pilih Perusahaan')
                        ->live()
                        ->createOptionForm(self::getCorporateForm())
                        ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

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
                                ->searchable()->required()
                                ->createOptionForm(self::getPeroranganForm()) // Asumsikan Anda punya helper method ini
                                ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id),
                        ])
                        ->minItems(1)
                        ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                        ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                        ->addActionLabel('Tambah PIC')
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
                ]),

            Section::make('Lokasi Proyek')->schema(self::getAddressFields())->columns(2),
            Section::make('Keuangan & Status')->schema(self::getKeuanganFields())->columns(2),
            Hidden::make('user_id')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_project')->sortable()->searchable()->wrap(),
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Klien Utama')
                    ->state(function (Project $record): string {
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
                Tables\Columns\TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2),
                Tables\Columns\TextColumn::make('status')->sortable()->badge(),
                Tables\Columns\TextColumn::make('status_pembayaran')->label('Pembayaran')->badge()->color(fn(string $state): string => match ($state) {
                    'Lunas' => 'success',
                    'Belum Lunas' => 'danger',
                    default => 'warning'
                }),
                Tables\Columns\TextColumn::make('status_pekerjaan')->label('Pekerjaan')->badge()->color(fn(string $state): string => $state === 'Selesai' ? 'success' : 'warning'),
            ])
            ->defaultSort('created_at', 'desc')
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

            Forms\Components\Textarea::make('detail_alamat')
                ->required()
                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    private static function getKeuanganFields(): array
    {
        return [
            Forms\Components\TextInput::make('nilai_project')
                ->label('Anggaran Proyek')
                ->numeric()
                ->required()
                ->placeholder('Masukkan anggaran proyek')
                ->prefix('Rp ')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->maxlength(20),
            Select::make('status')
                ->label('Status Proyek')
                ->options([
                    'Prospect' => 'Prospect',
                    'Follow up 1' => 'Follow up 1',
                    'Follow up 2' => 'Follow up 2',
                    'Follow up 3' => 'Follow up 3',
                    'Closing' => 'Closing',
                ])
                ->required()
                ->native(false),
        ];
    }

    private static function getSalesForm(): array
    {
        return [
            Forms\Components\Section::make('Informasi Sales')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Sales')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->required()
                        ->length(16)
                        ->unique(ignoreRecord: true)
                        ->numeric(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('telepon')
                        ->label('Telepon')
                        ->required()
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
                        ->required()
                        ->maxLength(200),
                    Forms\Components\TextInput::make('npwp')
                        ->label('NPWP')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('telepon')
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
            Forms\Components\Section::make('Informasi Personal')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
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
                ])->columns(2),

            Forms\Components\Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getKategoriForm(): array
    {
        return [
            Forms\Components\Section::make('Informasi Kategori')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->maxLength(500)
                        ->nullable(),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola layanan pemetaan'); // atau permission spesifik
    }
}
