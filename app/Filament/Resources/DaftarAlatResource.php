<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarAlatResource\Pages;
use App\Models\DaftarAlat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\DB;
use App\Models\TrefRegion;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;


class DaftarAlatResource extends Resource
{
    protected static ?string $model = DaftarAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationLabel = 'Daftar Alat';

    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?int $navigationGroupSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_alat_id')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->required(),
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ]),
                Forms\Components\TextInput::make('nomor_seri')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->validationMessages([
                        'unique' => 'Nomor seri ini sudah terdaftar, silakan gunakan yang lain.',
                    ])
                    ->required(),
                Forms\Components\Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\Select::make('pemilik_id')
                    ->relationship('pemilik', 'nama')
                    ->searchable()
                    ->preload()
                    // Menambahkan form modal untuk membuat pemilik baru
                    ->createOptionForm([
                        Section::make('Informasi Pribadi')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label(label: 'Nama Pemilik (Sesuai KTP)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('gender')
                                    ->dehydrated()
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Pria' => 'Pria',
                                        'Wanita' => 'Wanita',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('NIK')
                                    ->label('Nomor Induk Kependudukan (NIK)')
                                    ->string()
                                    ->minLength(16)
                                    ->maxLength(16)
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required(),
                            ])->columns(2),

                        Section::make('Alamat')
                            ->schema([
                                Select::make('provinsi')->label('Provinsi')->required()->placeholder('Pilih Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null) && $set('kecamatan', null) && $set('desa', null)),
                                Select::make('kota')->label('Kota/Kabupaten')->required()->placeholder('Pilih Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null) && $set('desa', null)),
                                Select::make('kecamatan')->label('Kecamatan')->required()->placeholder('Pilih Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                                Select::make('desa')->label('Desa/Kelurahan')->required()->placeholder('Pilih Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                                Textarea::make('detail_alamat')->label('Detail Alamat')->required()->columnSpanFull()->placeholder('cth: Jl. Supriyadi No,12, RT.3/RW.4'),
                            ])->columns(2),
                    ])
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Dipakai',
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri')
                    ->searchable()
                    ->sortable()
                    ->label('Nomor Seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')
                    ->searchable()
                    ->label('Jenis Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('merk.nama')
                    ->searchable()
                    ->label('Merk Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemilik.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Pemilik Alat'),

                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),

                BadgeColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Tersedia' : 'Dipakai')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ]),
                TernaryFilter::make('kondisi')
                    ->label('Kondisi')
                    ->placeholder('Semua Kondisi')
                    ->trueLabel('Baik')
                    ->falseLabel('Bermasalah'),

                TernaryFilter::make('status')
                    ->label('Ketersediaan')
                    ->placeholder('Semua Status')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaftarAlats::route('/'),
            'create' => Pages\CreateDaftarAlat::route('/create'),
            'edit' => Pages\EditDaftarAlat::route('/{record}/edit'),
        ];
    }
}
