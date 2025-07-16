<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\Pages;
use App\Filament\Resources\CorporateResource\RelationManagers;
use App\Models\Corporate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CorporateResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\ProjectsRelationManager;

class CorporateResource extends Resource
{
    protected static ?string $model = Corporate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')->required(),
                Forms\Components\TextInput::make('nib')->unique()->nullable(),
                Forms\Components\Select::make('level')
                    ->required()
                    ->options([
                        'besar' => 'Besar',
                        'menengah' => 'Menengah',
                        'kecil' => 'Kecil',
                    ]),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('telepon')->tel()->required(),
                Forms\Components\TextInput::make('alamat')->required(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
            SewaRelationManager::class,
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
