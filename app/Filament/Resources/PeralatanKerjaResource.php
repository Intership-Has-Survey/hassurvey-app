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
                    ->required()
                    ->maxLength(100),
                TextInput::make('jenis_alat')
                    ->label('Jenis Alat')
                    ->required()
                    ->maxLength(50),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.nama_project')->label('Nama Projek'),
                TextColumn::make('nama_alat'),
                TextColumn::make('jenis_alat'),
                TextColumn::make('jumlah'),
                TextColumn::make('user.name')->label('Nama User'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPeralatanKerjas::route('/'),
            'create' => Pages\CreatePeralatanKerja::route('/create'),
            'edit' => Pages\EditPeralatanKerja::route('/{record}/edit'),
        ];
    }
}
