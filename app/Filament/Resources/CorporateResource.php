<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use App\Models\Corporate;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\CorporateResource\Pages;
use Filament\Resources\RelationManagers\RelationGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\CorporateResource\Pages\EditCorporate;
use App\Filament\Resources\CorporateResource\Pages\ListCorporates;
use App\Filament\Resources\CorporateResource\Pages\CreateCorporate;
use App\Filament\Resources\CorporateResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\PeroranganRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\RiwayatKalibrasisRelationManager;
use App\Filament\Resources\CorporateResource\RelationManagers\RiwayatPenjualansRelationManager;

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Perusahaan')
                    ->searchable(),
                TextColumn::make('telepon')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')->label('Editor')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // DeleteAction::make(),
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
            ->emptyStateHeading('Belum Ada Pelanggan bertipe Perusahaan yang Terdaftar')
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewCorporate::route('/{record}'),
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
