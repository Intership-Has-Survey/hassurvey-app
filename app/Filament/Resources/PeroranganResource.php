<?php

namespace App\Filament\Resources;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\PeroranganResource\Pages;
use Filament\Resources\RelationManagers\RelationGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\PeroranganResource\Pages\EditPerorangan;
use App\Filament\Resources\PeroranganResource\Pages\ListPerorangans;
use App\Filament\Resources\PeroranganResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\CorporateRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatLayananRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatKalibrasisRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\RiwayatPenjualansRelationManager;

class PeroranganResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Perorangan::class;
    // protected static bool $shouldRegisterNavigation = false;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Perorangan';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?int $navigationSort = 2;
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
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Klien')
                    ->searchable(),
                TextColumn::make('telepon')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')->label('Editor')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filter([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pelanggan bertipe Perorangan yang Terdaftar')
            ->defaultSort('created_at', 'desc');
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
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
