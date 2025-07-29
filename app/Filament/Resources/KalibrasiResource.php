<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KalibrasiResource\Pages;
use App\Filament\Resources\KalibrasiResource\RelationManagers;
use App\Models\Kalibrasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KalibrasiResource\RelationManagers\DetailKalibrasiRelationManager;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Perorangan;
use App\Models\Corporate;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrefRegion;
use Illuminate\Support\Facades\DB;

class KalibrasiResource extends Resource
{
    protected static ?string $model = Kalibrasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Kalibrasi';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Jasa Kalibrasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Kalibrasi')
                    ->required(),
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
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'progress' => 'progress',
                        'selesai' => 'selesai'
                    ])
                    ->native(false),
                Hidden::make('harga')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('customer_id'),
                Tables\Columns\TextColumn::make('status'),
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
            //
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
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
