<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Penjualan;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class PenjualanResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Penjualan')
                    ->schema([
                        TextInput::make('nama_penjualan')
                            ->label('Nama Penjualan')
                            ->required(),
                        DatePicker::make('tanggal_penjualan')
                            ->required()
                            ->default(now())
                            ->label('Tanggal Penjualan')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Select::make('sales_id')
                            ->relationship('sales', 'nama', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                            ->label('Sales')
                            ->getOptionLabelFromRecordUsing(fn(Sales $record) => "{$record->nama} - {$record->nik}")
                            ->placeholder('Pilih sales')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(self::getSalesForm()),

                        Textarea::make('catatan'),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])->columns(2),
                Section::make('Informasi Customer')
                    ->schema(self::getCustomerForm()),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_penjualan')->searchable()
                    ->label('Nama Penjualan')
                    ->sortable(),
                TextColumn::make('tanggal_penjualan')->date(),
                TextColumn::make('customer_display')
                    ->label('Klien')
                    ->state(function (Penjualan $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        return $record->perorangan->first()?->nama ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2),
                TextColumn::make('sales.nama')->label('Sales'),
                TextColumn::make('status_pembayaran')->label('Pembayaran')->badge()->color(fn(string $state): string => match ($state) {
                    'Lunas' => 'success',
                    'Belum Lunas' => 'danger',
                    default => 'info',
                }),
                TextColumn::make('total_items')
                    ->label('Total Item'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPenjualanRelationManager::class,
            RelationManagers\StatusPembayaranRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
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
