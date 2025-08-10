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

        $uuid = request()->segment(2);
        return $form
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
                Select::make('customer_flow_type')
                    ->label('Tipe Customer')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tipe Customer harus dipilih',
                    ])
                    ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                    ->live()->dehydrated(false)->native(false)
                    ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null))
                    ->columnSpanFull()->required()
                    ->validationMessages([
                        'required' => 'Customer tidak boleh kosong',
                    ]),
                Select::make('corporate_id')
                    ->label('Pilih Perusahaan')
                    ->options(
                        Corporate::whereNotNull('nama')->pluck('nama', 'id')
                    )
                    ->live()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(self::getCorporateForm())
                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                    ->columnSpanFull()
                    ->required(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                    ->validationMessages([
                        'required' => 'Perusahaan wajib diisi',
                    ])
                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                Repeater::make('perorangan')
                    ->label(fn(Get $get): string => $get('customer_flow_type') === 'corporate' ? 'PIC' : 'Pilih Customer')
                    ->relationship()
                    ->required()
                    ->validationMessages([
                        'required' => 'Kolom Customer wajib diisi',
                    ])
                    ->schema([
                        Select::make('perorangan_id')
                            ->label(false)
                            ->required()
                            ->options(function (Get $get, $state): array {
                                $selectedPicIds = collect($get('../../perorangan'))->pluck('perorangan_id')->filter()->all();
                                $selectedPicIds = array_diff($selectedPicIds, [$state]);
                                return Perorangan::whereNotIn('id', $selectedPicIds)->get()->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])->all();
                            })
                            ->searchable()
                            ->createOptionForm(self::getPeroranganForm())
                            ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id),
                    ])
                    ->minItems(1)
                    ->distinct()
                    ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                    ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                    ->addActionLabel('Tambah PIC')
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                    ->saveRelationshipsUsing(function (Model $record, array $state): void {
                        $ids = array_map(fn($item) => $item['perorangan_id'], $state);
                        $peran = $record->corporate_id ? $record->corporate->nama : 'Pribadi';

                        // Sync dengan project dan simpan peran
                        $syncData = [];
                        foreach ($ids as $id) {
                            $syncData[$id] = ['peran' => $peran];
                        }
                        $record->perorangan()->sync($syncData);

                        if ($record->corporate_id) {
                            $corporate = $record->corporate;
                            foreach ($ids as $peroranganId) {
                                if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
                                    $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                                }
                            }
                        }
                    })
                    ->rules(['required', 'uuid']),
                Select::make('sales_id')
                    ->relationship('sales', 'nama')
                    ->label('Sales')
                    ->options(function () {
                        return Sales::query()
                            ->select('id', 'nama', 'nik')
                            ->get()
                            ->mapWithKeys(fn($sales) => [$sales->id => "{$sales->nama} - {$sales->nik}"]);
                    })
                    ->placeholder('Pilih sales')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(self::getSalesForm()),

                Textarea::make('catatan'),
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
