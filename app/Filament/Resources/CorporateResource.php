<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\TrefRegion;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\CorporateResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationGroup;
use App\Filament\Resources\CorporateResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\PeroranganRelationManager;

class CorporateResource extends Resource
{
    protected static ?string $model = Corporate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Perusahaan')
                    ->schema([
                        Forms\Components\TextInput::make('nama')->required(),
                        Forms\Components\TextInput::make('nib')->unique()->nullable()->label('NIB')
                            ->placeholder('Nomor Induk Berusaha (NIB)'),
                        Forms\Components\Select::make('level')
                            ->required()
                            ->options([
                                'Besar' => 'Besar',
                                'Menengah' => 'Menengah',
                                'Kecil' => 'Kecil',
                            ]),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('telepon')->tel()->required(),
                    ])->columns(2),
                Section::make('Alamat Perusahaan')
                    ->schema([
                        Select::make('provinsi')->label('Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null)),
                        Select::make('kota')->label('Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null)),
                        Select::make('kecamatan')->label('Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                        Select::make('desa')->label('Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                        Textarea::make('detail_alamat')->label('Detail Alamat')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PeroranganRelationManager::class,
            RelationGroup::make('Riwayat Pesanan', [
                ProjectsRelationManager::class,
                SewaRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCorporates::route('/'),
            'create' => Pages\CreateCorporate::route('/create'),
            'edit' => Pages\EditCorporate::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view-any Project'); // atau permission spesifik
    }
}
