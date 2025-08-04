<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatLayananRelationManager;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PeroranganResource\Pages;
use Filament\Resources\RelationManagers\RelationGroup;
use App\Filament\Resources\PeroranganResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\CorporateRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatKalibrasisRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatPenjualansRelationManager;

class PeroranganResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Perorangan::class;
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Pribadi')
                ->schema([
                    TextInput::make('nama')->required(),
                    TextInput::make('nik')->unique()->nullable(),
                    Select::make('gender')
                        ->required()
                        ->options([
                            'Pria' => 'Pria',
                            'Wanita' => 'Wanita'
                        ]),
                    TextInput::make('email')->email(),
                    TextInput::make('telepon')->tel()->required(),
                ])->columns(2),
            Section::make('Alamat')
                ->schema(self::getAddressFields())->columns(2),
            Section::make('Dokumen Identitas')
                ->schema([
                    FileUpload::make('foto_ktp')->image()->nullable(),
                    FileUpload::make('foto_kk')->image()->nullable(),
                ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            CorporateRelationManager::class,
            ProjectsRelationManager::class,
            SewaRelationManager::class,
            RiwayatKalibrasisRelationManager::class,
            RiwayatPenjualansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => Pages\EditPerorangan::route('/{record}/edit'),
            'index' => Pages\ListPerorangans::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view-any Project'); // atau permission spesifik
    }
}
