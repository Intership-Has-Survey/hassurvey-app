<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProdukResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProdukResource\RelationManagers;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('jenis_alat_id')
                    ->label('Jenis Alat')
                    ->relationship('jenisAlat', 'nama')
                    ->required()
                    ->validationMessages(['required' => 'Jenis Alat wajib diisi']),
                Select::make('merk_id')
                    ->label('Merk')
                    ->relationship('merk', 'nama')
                    ->required()
                    ->validationMessages(['required' => 'Merk wajib diisi']),
                TextInput::make('nomor_seri')
                    ->label('Nomor Seri')
                    ->required()
                    ->validationMessages(['required' => 'Nomor Seri wajib diisi']),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey() ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('jenisAlat.nama')->label('Jenis Alat')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('merk.nama')->label('Merk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nomor_seri')->label('Nomor Seri')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('keterangan')->label('Keterangan')->limit(50)->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat Pada')->dateTime()->sortable(),
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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
