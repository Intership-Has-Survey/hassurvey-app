<?php

namespace App\Filament\Resources;

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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\RelationManagers\PersonelsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPekerjaanRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPembayaranRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\DaftarAlatProjectRelationManager;

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
            Forms\Components\Section::make('Informasi Proyek')
                ->schema([
                    Forms\Components\TextInput::make('nama_project')->required()->columnSpanFull(),
                    Forms\Components\Select::make('kategori_id')->relationship('kategori', 'nama')->searchable()->preload()->required()->createOptionForm(self::getKategoriForm()),
                    Forms\Components\Select::make('sales_id')->relationship('sales', 'nama')->searchable()->preload()->required()->createOptionForm(self::getSalesForm()),
                    Forms\Components\DatePicker::make('tanggal_informasi_masuk')->required()->native(false)->default(now()),
                    Forms\Components\Select::make('sumber')->options(['Online' => 'Online', 'Offline' => 'Offline'])->required()->native(false),
                ])->columns(2),

            Forms\Components\Section::make('Informasi Customer')
                ->schema([
                    Forms\Components\Select::make('customer_flow_type')
                        ->label('Tipe Customer')
                        ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                        ->live()->required()->dehydrated(false)
                        ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null)),

                    Forms\Components\Select::make('corporate_id')
                        ->relationship('corporate', 'nama')
                        ->label('Pilih Perusahaan')
                        ->live()
                        ->createOptionForm([ /* ... */])
                        ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                    Forms\Components\Repeater::make('perorangan')
                        ->label(fn(Get $get): string => $get('customer_flow_type') === 'corporate' ? 'PIC' : 'Customer')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('perorangan_id')
                                ->label(false) // Label disembunyikan untuk tampilan simple
                                ->options(function (Get $get, $state): array {
                                    $selectedPicIds = collect($get('../../perorangan'))->pluck('perorangan_id')->filter()->all();
                                    $selectedPicIds = array_diff($selectedPicIds, [$state]);
                                    return Perorangan::whereNotIn('id', $selectedPicIds)->pluck('nama', 'id')->all();
                                })
                                ->searchable()->required()
                                ->createOptionForm([ /* ... */])
                                ->createOptionUsing(function (array $data): string {
                                    $data['user_id'] = auth()->id();
                                    return Perorangan::create($data)->id;
                                }),
                        ])
                        ->minItems(1)
                        ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                        ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                        ->addActionLabel('Tambah PIC')
                        // ->simple()
                        ->itemLabel(fn(array $state): ?string => Perorangan::find($state['perorangan_id'])?->nama)
                        ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                        ->saveRelationshipsUsing(function (Model $record, array $state): void {
                            $ids = array_map(fn($item) => $item['perorangan_id'], $state);
                            $record->perorangan()->sync($ids);
                        }),
                ]),

            // ... (Section Keuangan & Status)

            Forms\Components\Section::make('Lokasi Proyek')->schema(self::getAddressFields())->columns(2),

            Forms\Components\Section::make('Keuangan & Status')
                ->schema([
                    Forms\Components\TextInput::make('nilai_project')->numeric()->prefix('Rp')->required()->mask(RawJs::make('$money($input)'))->stripCharacters(','),
                    Forms\Components\Select::make('status')->label('Status Prospek')->options(['Prospect' => 'Prospect', 'Follow up' => 'Follow up', 'Closing' => 'Closing'])->required()->native(false),
                ])->columns(2),

            Forms\Components\Hidden::make('user_id')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_project')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama')->sortable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Klien/Perusahaan')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph('customer', [Perorangan::class, Corporate::class], fn(Builder $q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('status')->sortable()->badge(),
                Tables\Columns\TextColumn::make('status_pembayaran')->badge()->color(fn(string $state): string => match ($state) {
                    'Lunas' => 'success',
                    'Belum Lunas' => 'danger',
                    default => 'warning'
                }),
                Tables\Columns\TextColumn::make('status_pekerjaan')->badge()->color(fn(string $state): string => $state === 'Selesai' ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Prospect' => 'Prospect',
                        'Follow up 1' => 'Follow up 1',
                        'Follow up 2' => 'Follow up 2',
                        'Follow up 3' => 'Follow up 3',
                        'Closing' => 'Closing'
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status_pembayaran')
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

    private static function getKategoriForm(): array
    {
        return [
            Section::make('Informasi Kategori')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(100),
                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->maxLength(500)
                        ->nullable(),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }
}
