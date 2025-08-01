<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use App\Models\TransaksiPembayaran;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiPembayaranResource\Pages;
use App\Filament\Resources\TransaksiPembayaranResource\RelationManagers;
use App\Filament\Resources\TransaksiPembayaranResource\Pages\ListTransaksiPembayarans;
use App\Filament\Resources\TransaksiPembayaranResource\Pages\CreateTransaksiPembayaran;

class TransaksiPembayaranResource extends Resource
{
    protected static ?string $model = TransaksiPembayaran::class;

    // protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Semua Pengeluaran';
    protected static ?string $title = 'Semua Pengeluaran';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('pengajuan_dana_id')
                //     ->relationship('pengajuanDana', 'judul_pengajuan')
                //     ->searchable()
                //     ->preload()
                //     ->required(),
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
                Tables\Columns\TextColumn::make('payable_type')
                    ->label('Jenis Pengeluaran')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\\Models\\PengajuanDana' => 'Pengajuan Dana',
                        default => 'Lainnya'
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->badge(),
                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat oleh')
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
