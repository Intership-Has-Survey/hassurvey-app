<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JenisPekerjaan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\JenisPekerjaanResource\Pages;
use App\Filament\Resources\JenisPekerjaanResource\RelationManagers;

class JenisPekerjaanResource extends Resource
{
    protected static ?string $model = JenisPekerjaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Jenis Pekerjaan';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('nama')->required()->label('Jenis Pekerjaan'),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(300),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextInput::make('nama_project')->required(),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('user.name')->label('Editor')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListJenisPekerjaans::route('/'),
            'create' => Pages\CreateJenisPekerjaan::route('/create'),
            'edit' => Pages\EditJenisPekerjaan::route('/{record}/edit'),
        ];
    }
}
