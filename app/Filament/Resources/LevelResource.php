<?php

namespace App\Filament\Resources;

use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
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
use App\Filament\Resources\LevelResource\Pages\EditLevel;
use App\Filament\Resources\LevelResource\Pages\ListLevels;
use App\Filament\Resources\LevelResource\Pages\CreateLevel;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\LevelResource\RelationManagers\LevelStepRelationManager;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $pluralModelLabel = 'Level Pengajuan';

    protected static ?string $navigationLabel = 'Level Pengajuan';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $tenantRelationshipName = 'Levels';

    public static function form(Form $form): Form
    {

        $uuid = request()->segment(2);
        return $form
            ->schema([
                //
                TextInput::make('nama')
                    ->required()
                    ->label('Level pengajuan')
                    ->validationMessages([
                        'required' => 'Kolom ini wajib diisi',
                    ]),
                TextInput::make('max_nilai')
                    ->label('Maksimal Pengajuan Sebesar')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->validationMessages([
                        'required' => 'Kolom ini wajib diisi',
                        'min' => 'Tidak boleh kurang dari 0',
                    ]),
                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('max_nilai')->label('Maksimal Pengajuan')->money('IDR'),
            ])
            ->filters([
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
            ->emptyStateHeading('Belum Ada Level Pengajuan')
            ->emptyStateDescription('Silahkan buat Level pengajuan baru untuk memulai.');
    }

    public static function getRelations(): array
    {
        return [
            LevelStepRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLevels::route('/'),
            'create' => CreateLevel::route('/create'),
            'edit' => EditLevel::route('/{record}/edit'),
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
