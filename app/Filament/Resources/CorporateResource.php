<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\Pages;
use App\Filament\Resources\CorporateResource\RelationManagers;
use App\Models\Corporate;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CorporateResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\PeroranganRelationManager;
use Filament\Resources\RelationManagers\RelationGroup;

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
                        Forms\Components\TextInput::make('provinsi')->required()->maxLength(2),
                        Forms\Components\TextInput::make('kota')->required()->maxLength(5),
                        Forms\Components\TextInput::make('kecamatan')->required()->maxLength(8),
                        Forms\Components\TextInput::make('desa')->required()->maxLength(13),
                        Forms\Components\TextInput::make('detail_alamat')->required(),
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
}
