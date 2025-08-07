<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\RelationManagers\RiwayatKalibrasisRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\RiwayatPenjualansRelationManager;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use App\Filament\Resources\CorporateResource\Pages;
use Filament\Resources\RelationManagers\RelationGroup;
use App\Filament\Resources\CorporateResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\PeroranganRelationManager;

class CorporateResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Corporate::class;
    protected static ?string $navigationLabel = 'Perusahaan';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?string $title = 'Customer Perusahaan';
    protected static ?string $pluralModelLabel = 'Perusahaan';
    protected static ?int $navigationSort = 1;

    // protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $uuid = request()->segment(2);
        return $form
            ->schema([
                Section::make('Informasi Perusahaan')
                    ->schema([
                        TextInput::make('nama')->required(),
                        TextInput::make('nib')->unique()->nullable()->label('NIB')
                            ->placeholder('Nomor Induk Berusaha (NIB)'),
                        Select::make('level')
                            ->required()
                            ->options([
                                'Besar' => 'Besar',
                                'Menengah' => 'Menengah',
                                'Kecil' => 'Kecil',
                            ]),
                        TextInput::make('email')->email(),
                        TextInput::make('telepon')->tel()->required(),
                    ])->columns(2),
                Section::make('Alamat Perusahaan')
                    ->schema(self::getAddressFields())->columns(2),
                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PeroranganRelationManager::class,
            ProjectsRelationManager::class,
            SewaRelationManager::class,
            RiwayatKalibrasisRelationManager::class,
            RiwayatPenjualansRelationManager::class,

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
