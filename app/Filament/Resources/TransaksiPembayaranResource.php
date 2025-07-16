<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use App\Models\TransaksiPembayaran;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiPembayaranResource\Pages;
use App\Filament\Resources\TransaksiPembayaranResource\RelationManagers;

class TransaksiPembayaranResource extends Resource
{
    protected static ?string $model = TransaksiPembayaran::class;

    // protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    // protected static ?string $navigationGroup = 'Keuangan';
    // protected static ?string $navigationLabel = 'Riwayat Transaksi Keluar';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengajuan_dana_id')
                    ->relationship('pengajuanDana', 'judul_pengajuan')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp')
                    ->maxlength(20),
                Forms\Components\DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('metode_pembayaran')
                    ->options([
                        'Transfer' => 'Transfer',
                        'Tunai' => 'Tunai',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran')
                    ->image(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuanDana.judul_pengajuan')
                    ->label('Untuk Pengajuan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibayar oleh')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTransaksiPembayarans::route('/'),
            'create' => Pages\CreateTransaksiPembayaran::route('/create'),
            // 'view' => Pages\ViewTransaksiPembayaran::route('/{record}'),
            'edit' => Pages\EditTransaksiPembayaran::route('/{record}/edit'),
        ];
    }
}
