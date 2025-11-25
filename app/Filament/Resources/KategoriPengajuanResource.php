<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriPengajuanResource\Pages;
use App\Filament\Resources\KategoriPengajuanResource\RelationManagers;
use App\Models\KategoriPengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriPengajuanResource extends Resource
{
    protected static ?string $model = KategoriPengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\TextInput::make('code')
                    ->label('Kode Kategori ssssfs')
                    ->disabled() // biar user tidak bisa ubah manual
                    ->dehydrated(false) // jangan simpan input dari user
                    ->visibleOn(['edit', 'view'])
                    ->columnSpan(2),
                Forms\Components\TextInput::make('parent_id')
                    ->label('Parent ID')
                    ->disabled() // biar user tidak bisa ubah manual
                    ->dehydrated(false) // jangan simpan input dari user
                    ->visibleOn(['edit', 'view'])
                    ->maxLength(5),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->maxLength(100)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListKategoriPengajuans::route('/'),
            'create' => Pages\CreateKategoriPengajuan::route('/create'),
            'edit' => Pages\EditKategoriPengajuan::route('/{record}/edit'),
        ];
    }
}
