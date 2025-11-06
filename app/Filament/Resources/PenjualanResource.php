<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use Filament\Forms\Components\Grid;
use App\Models\JenisAlat;
use App\Models\Penjualan;
use App\Models\DaftarAlat;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Pages\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\PenjualanResource\Pages\EditPenjualan;
use App\Filament\Resources\PenjualanResource\Pages\ListPenjualans;
use App\Filament\Resources\PenjualanResource\Pages\CreatePenjualan;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Filament\Resources\PenjualanResource\RelationManagers\DetailPenjualanRelationManager;
use App\Filament\Resources\PenjualanResource\RelationManagers\StatusPembayaranRelationManager;

class PenjualanResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {

        $uuid = request()->segment(2);
        return $form
            ->schema([
                Section::make('Informasi Penjualan')
                    ->schema([
                        TextInput::make('kode_penjualan')
                            ->label('Kode Penjualan')
                            ->disabled() // biar user tidak bisa ubah manual
                            ->dehydrated(false) // jangan simpan input dari user
                            ->visibleOn(['edit', 'view']),
                        TextInput::make('nama_penjualan')
                            ->label('Nama Penjualan')
                            ->required(),
                        DatePicker::make('tanggal_penjualan')
                            ->required()
                            ->default(now())
                            ->label('Tanggal Penjualan')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Grid::make(2)
                            ->schema([
                                Select::make('sales_id')
                                    ->relationship('sales', 'nama', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                                    ->label('Sales')
                                    ->getOptionLabelFromRecordUsing(fn(Sales $record) => "{$record->nama} - {$record->nik}")
                                    ->placeholder('Pilih sales')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(self::getSalesForm()),
                                Select::make('sumber')
                                    ->options([
                                        'Online' => 'Online',
                                        'Offline' => 'Offline'
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'Sumber tidak boleh kosong',
                                    ]),
                            ]),

                        Textarea::make('catatan'),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])->columns(2),
                Section::make('Informasi Customer')
                    ->schema(self::getCustomerForm()),
                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_penjualan')->sortable()->searchable()->wrap(),
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
            ->headerActions([
                ExportAction::make('semua')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withColumns([
                                Column::make('kode_penjualan'),
                                Column::make('nama_penjualan'),
                                Column::make('sumber'),
                                Column::make('sales.nama')->heading('Sales'),
                                Column::make('corporate.nama')->heading('Klien'),
                                Column::make('catatan'),
                                Column::make('total_items'),
                            ])
                            ->withFilename(date('Y-m-d') . ' - penjualans-export')
                    ])
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
                ExportAction::make()
                    ->exports([
                        // $var = 1,
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make('form')
                            ->fromTable()
                            ->except(['total_items'])
                            ->modifyQueryUsing(function ($query, $livewire) {
                                return \App\Models\Penjualan::with(['statusPembayaran', 'pengajuanDanas', 'detailPenjualan'])
                                    ->where('id', $livewire->mountedTableActionRecord);
                            })
                            ->withColumns([
                                Column::make('statusPembayaran')
                                    ->heading('pendapatan')
                                    ->formatStateUsing(function ($state) {
                                        return $state->pluck('nilai')->sum();
                                    }),
                                Column::make('pengajuanDanas')
                                    ->heading('pengeluaran')
                                    ->formatStateUsing(function ($state, $record) {
                                        $totalPengajuanDana = $state->pluck('dibayar')->sum();
                                        return $totalPengajuanDana;
                                    }),
                                Column::make('detailPenjualan')
                                    ->heading('Daftar Alat')
                                    ->formatStateUsing(function ($state, $record) {
                                        $daftarAlat = $state->map(function ($alat) {
                                            // @dump($alat);
                                            return ($alat->jenisAlat->nama . ' ' . $alat->daftarAlat->nomor_seri);
                                        })->implode(', ');
                                        return $daftarAlat;
                                    }),
                                // ->formatStateUsing(function ($state, $record) {
                                //     $daftarAlat = $state->pluck('nomor_seri')->implode(', ');
                                //     return $daftarAlat;
                                // }),
                            ])
                            ->withFilename(function ($livewire) {
                                $penjualan = \App\Models\Penjualan::find($livewire->mountedTableActionRecord);

                                return ($penjualan->kode_penjualan ?: 'penjualan')
                                    . '-' . date('Y-m-d');
                            })
                    ])
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
            RelationManagers\DetailPenjualanRelationManager::class,
            RelationManagers\StatusPembayaranRelationManager::class,

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
            'view' => Pages\ViewPenjualan::route('/{record}'),
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
}
