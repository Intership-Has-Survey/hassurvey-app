<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SalesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SalesResource\RelationManagers;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 7;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sales')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Sales')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),
                Forms\Components\Section::make('Alamat')
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
                TextColumn::make('nama'),
                TextColumn::make('email'),
                TextColumn::make('telepon'),
                TextColumn::make('user.name')
                    ->label('Editor')
                    ->sortable()
                    ->searchable(),

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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }
}
