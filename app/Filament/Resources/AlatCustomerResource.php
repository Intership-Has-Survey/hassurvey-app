<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatCustomerResource\Pages;
use App\Filament\Resources\AlatCustomerResource\RelationManagers;
use App\Models\AlatCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;

class AlatCustomerResource extends Resource
{
    protected static ?string $model = AlatCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Customer')
                            ->required(),
                    ])
                    ->required(),
                Select::make('jenis_alat_id')
                    ->relationship('jenisalat', 'nama')
                    ->native(false),
                Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->native(false),
                TextInput::make('nomor_seri')
                    ->default(0),
                TextInput::make('keterangan')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_id'),
                TextColumn::make('jenis_alat_id'),
                TextColumn::make('merk_id'),
                TextColumn::make('nomor_seri'),
                TextColumn::make('keterangan'),
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
            'index' => Pages\ListAlatCustomers::route('/'),
            'create' => Pages\CreateAlatCustomer::route('/create'),
            'edit' => Pages\EditAlatCustomer::route('/{record}/edit'),
        ];
    }
}
