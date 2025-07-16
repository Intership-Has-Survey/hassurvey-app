<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Personel;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PersonelResource\RelationManagers;

class PersonelResource extends Resource
{
    protected static ?string $model = Personel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Personel';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('nik')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->numeric()
                            ->length(16)
                            ->required(),
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false),
                        TextInput::make('nomor_wa')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->tel(),
                    ])->columns(2),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Select::make('tipe_personel')
                            ->label('Tipe Personel')
                            ->options([
                                'internal' => 'Internal',
                                'freelance' => 'Freelance',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('jabatan')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->placeholder('Kosongkan jika tidak ada keterangan khusus')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Alamat')
                    ->schema([
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
                    ])->columns(2),

                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipe_personel')
                    ->label('Tipe Personel')
                    ->badge()
                    ->color(
                        fn(string $state): string =>
                        str_contains($state, 'internal') ? 'primary' : 'info'
                    )
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string =>
                        str_contains($state, 'dalam project') ? 'warning' : 'success'
                    ),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Editor')
                    ->sortable()
                    ->searchable(),
            ])

            ->filters([

                SelectFilter::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Personel::query()
                            ->select('jabatan')
                            ->distinct()
                            ->pluck('jabatan', 'jabatan');
                    }),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonels::route('/'),
            'create' => Pages\CreatePersonel::route('/create'),
            'edit' => Pages\EditPersonel::route('/{record}/edit'),
        ];
    }
}
