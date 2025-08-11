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
use Filament\Pages\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\TransaksiPembayaranResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\TransaksiPembayaranResource\Pages\ListTransaksiPembayarans;
use App\Filament\Resources\TransaksiPembayaranResource\Pages\CreateTransaksiPembayaran;

class TransaksiPembayaranResource extends Resource
{
    protected static ?string $model = TransaksiPembayaran::class;

    // protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';
    protected static ?string $navigationLabel = 'Pengeluaran';
    protected static ?string $title = 'Pengeluaran';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $pluralModelLabel = 'Pengeluaran';

    public static function form(Form $form): Form
    {
        $uuid = request()->segment(2);
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
                DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->native(false),
                Select::make('metode_pembayaran')
                    ->options([
                        'Transfer' => 'Transfer',
                        'Tunai' => 'Tunai',
                    ])
                    ->required()
                    ->native(false),
                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->maxSize(1024)
                    ->required()
                    ->disk('public')
                    ->directory('bukti-pembayaran')
                    ->columnSpanFull(),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxlength(500)
                    ->nullable(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Hidden::make('company_id')
                    ->default($uuid),
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
                        'App\\Models\\PembayaranPersonel' => 'Pembayaran Personel',
                        'App\\Models\\Pemilik' => 'Pembayaran Investor',
                        default => 'Lainnya'
                    })
                    ->sortable(),
                TextColumn::make('tanggal_transaksi')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('nilai')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('metode_pembayaran')
                    ->badge(),
                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),

            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
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
            'view' => Pages\ViewTransaksiPembayaran::route('/{record}'),
            'edit' => Pages\EditTransaksiPembayaran::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
