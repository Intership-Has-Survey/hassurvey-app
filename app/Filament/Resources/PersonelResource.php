<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Personel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PersonelResource\RelationManagers;

class PersonelResource extends Resource
{
    protected static ?string $model = Personel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Personel';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->relationship('project', 'nama_project')
                    ->label('Proyek')
                    ->required(),
                Select::make('pekerjaan_lapangan')
                    ->options([
                        'surveyor' => 'surveyor',
                        'asisten surveyor' => 'asisten surveyor',
                        'driver' => 'driver',
                        'drafter' => 'drafter',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('nama_personel')
                    ->label('Nama Personel')
                    ->required(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required(),
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

                TextColumn::make('jenis_personel')
                    ->label('Jenis Personel')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_personel')
                    ->label('Nama Personel')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable(),

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

                SelectFilter::make('jenis_personel')
                    ->label('Jenis Personel')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Personel::query()
                            ->select('jenis_personel')
                            ->distinct()
                            ->pluck('jenis_personel', 'jenis_personel');
                    }),

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


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonels::route('/'),
            'create' => Pages\CreatePersonel::route('/create'),
            'edit' => Pages\EditPersonel::route('/{record}/edit'),
        ];
    }
}
