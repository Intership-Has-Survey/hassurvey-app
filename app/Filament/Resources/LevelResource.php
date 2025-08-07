<?php

namespace App\Filament\Resources;

use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\LevelResource\Pages\EditLevel;
use App\Filament\Resources\LevelResource\Pages\ListLevels;
use App\Filament\Resources\LevelResource\Pages\CreateLevel;
use App\Filament\Resources\LevelResource\RelationManagers\LevelStepRelationManager;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Jenis Tingkatan Pengajuan';

    protected static ?string $navigationLabel = 'Jenis Tingkatan Pengajuan';

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
                    ->label('Nama tingkatan pengajuan')
                    ->validationMessages([
                        'required' => 'Kolom ini wajib diisi',
                    ]),
                TextInput::make('max_nilai')
                    ->label('Maksimal pengajuan')
                    ->numeric()
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->validationMessages([
                        'required' => 'Kolom ini wajib diisi',
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
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Jenis Tingkatan Pengajuan')
            ->emptyStateDescription('Silahkan buat jenis tingkatan pengajuan baru untuk memulai.');
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
}
