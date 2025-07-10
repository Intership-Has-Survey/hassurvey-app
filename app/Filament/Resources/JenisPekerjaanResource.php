<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisPekerjaanResource\Pages;
use App\Filament\Resources\JenisPekerjaanResource\RelationManagers;
use App\Models\JenisPekerjaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;

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
                Forms\Components\TextInput::make('nama')->required()->label('Jenis Pekerjaan'),
                Forms\Components\TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(300),
                TextInput::make('user_id')
                    ->label('User')
                    ->required()
                    ->readOnly()
                    ->hint('tidak perlu diisi')
                    ->default(auth()->user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Forms\Components\TextInput::make('nama_project')->required(),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('user.name')->label('Editor')
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
