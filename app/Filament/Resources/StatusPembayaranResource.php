<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StatusPembayaran;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StatusPembayaranResource\Pages;
use App\Filament\Resources\StatusPembayaranResource\RelationManagers;

class StatusPembayaranResource extends Resource
{
    protected static ?string $model = StatusPembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Status Pembayaran';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_pembayaran'),
                TextInput::make('jenis_pembayaran'),
                TextInput::make('nilai'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_pembayaran')
                    ->label('Nama Pembayaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->options(function () {
                        return \App\Models\StatusPembayaran::query()
                            ->select('jenis_pembayaran')
                            ->distinct()
                            ->pluck('jenis_pembayaran', 'jenis_pembayaran');
                    })
                    ->searchable(),
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
            'index' => Pages\ListStatusPembayarans::route('/'),
            'create' => Pages\CreateStatusPembayaran::route('/create'),
            'edit' => Pages\EditStatusPembayaran::route('/{record}/edit'),
        ];
    }
}
