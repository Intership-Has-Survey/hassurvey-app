<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemilikResource\Pages;
use App\Filament\Resources\PemilikResource\RelationManagers;
use App\Models\Pemilik;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Models\TrefRegion;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Textarea;

class PemilikResource extends Resource
{
    protected static ?string $model = Pemilik::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pemilik Alat';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NIK')
                    ->label('Nomor Induk Kependudukan (NIK)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal Dibuat'),
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
            RelationManagers\DaftarAlatRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemiliks::route('/'),
            'create' => Pages\CreatePemilik::route('/create'),
            'edit' => Pages\EditPemilik::route('/{record}/edit'),
        ];
    }
}
