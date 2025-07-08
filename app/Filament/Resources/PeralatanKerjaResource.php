<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PeralatanKerja;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PeralatanKerjaResource\Pages;
use App\Filament\Resources\PeralatanKerjaResource\RelationManagers;

class PeralatanKerjaResource extends Resource
{
    protected static ?string $model = PeralatanKerja::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Peralatan Kerja';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->relationship('project', 'nama_project')
                    ->label('Proyek')
                    ->required(),
                TextInput::make('nama_alat')
                    ->label('Nama Alat')
                    ->required(),
                Select::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric(),
                TextInput::make('keterangan')
                    ->label('keterangan')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.nama_project')
                    ->label('Nama Proyek')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_alat')
                    ->label('Nama Alat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_alat')
                    ->label('Jenis Alat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->sortable()
                    ->searchable(),
            ])

            ->filters([
                SelectFilter::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama_project')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('jenis_alat')
                    ->label('Jenis Alat')
                    ->options(function () {
                        return \App\Models\PeralatanKerja::query()
                            ->select('jenis_alat')
                            ->distinct()
                            ->pluck('jenis_alat', 'jenis_alat');
                    })
                    ->searchable(),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
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
            'index' => Pages\ListPeralatanKerjas::route('/'),
            'create' => Pages\CreatePeralatanKerja::route('/create'),
            'edit' => Pages\EditPeralatanKerja::route('/{record}/edit'),
        ];
    }
}
