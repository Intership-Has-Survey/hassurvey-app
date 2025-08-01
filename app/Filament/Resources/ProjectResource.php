<?php

namespace App\Filament\Resources;

use App\Models\Sales;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\PersonelsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPekerjaanRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPembayaranRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\DaftarAlatProjectRelationManager;

class ProjectResource extends Resource
{
    use GlobalForms;

    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Layanan Pemetaan';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Proyek Pemetaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Proyek')
                ->schema([
                    TextInput::make('nama_project')->columnSpanFull(),
                    Select::make('kategori_id')->relationship('kategori', 'nama')->searchable()->preload()
                        ->createOptionForm(self::getKategoriForm()),
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
                    DatePicker::make('tanggal_informasi_masuk')->native(false)->default(now()),
                    Select::make('sumber')->options(['Online' => 'Online', 'Offline' => 'Offline'])->native(false),
                ])
                ->columns(2)
                ->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),

            Section::make('Informasi Customer')
                ->schema([
                    Select::make('customer_flow_type')
                        ->label('Tipe Customer')
                        ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                        ->live()->dehydrated(false)->native(false)
                        ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null)),

                    Select::make('corporate_id')
                        ->relationship('corporate', 'nama')
                        ->label('Pilih Perusahaan')
                        ->live()
                        ->searchable()
                        ->preload()
                        ->createOptionForm(self::getCorporateForm())
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (! $state) {
                                $set('perorangan', []);
                                return;
                            }

                            $corporate = \App\Models\Corporate::with('perorangan')->find($state);

                            if (! $corporate) {
                                $set('perorangan', []);
                                return;
                            }

                            $perorangan = $corporate->perorangan->map(fn($p) => [
                                'perorangan_id' => $p->id,
                            ])->toArray();

                            $set('perorangan', $perorangan);
                        })
                        ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                    Repeater::make('perorangan')
                        ->label(fn(Get $get): string => $get('customer_flow_type') === 'corporate' ? 'PIC' : 'Pilih Customer')
                        ->relationship()
                        ->schema([
                            Select::make('perorangan_id')
                                ->label(false)
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
                        ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                        ->saveRelationshipsUsing(function (Model $record, array $state): void {
                            $selectedIds = array_map(fn($item) => $item['perorangan_id'], $state);
                            $record->perorangan()->sync($selectedIds); // sync dengan project

                            if ($record->corporate_id) {
                                $corporate = $record->corporate;

                                // Ambil semua ID PIC yang terhubung sebelumnya
                                $existingIds = $corporate->perorangan()->pluck('perorangan_id')->toArray();

                                // Tambahkan PIC baru yang belum terhubung
                                foreach ($selectedIds as $peroranganId) {
                                    if (!in_array($peroranganId, $existingIds)) {
                                        $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                                    }
                                }

                                // Hapus PIC yang tidak ada di list sekarang
                                $toDetach = array_diff($existingIds, $selectedIds);
                                if (!empty($toDetach)) {
                                    $corporate->perorangan()->detach($toDetach);
                                }
                            }
                        })
                ])
                ->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),

            Section::make('Lokasi Proyek')->schema(self::getAddressFields())->columns(2)->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),
            Section::make('Keuangan & Status')->schema(self::getKeuanganFields())->columns(2)->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),


            Hidden::make('user_id')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')->sortable()->searchable()->wrap(),
                TextColumn::make('customer_display')
                    ->label('Klien Utama')
                    ->state(function (Project $record): string {
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
                TextColumn::make('status')->sortable()
                    ->badge()
                    ->icon(fn(string $state): ?string => match ($state) {
                        'Prospect' => 'heroicon-o-user-group',
                        'Follow up 1' => 'heroicon-o-clock',
                        'Follow up 2' => 'heroicon-o-clock',
                        'Follow up 3' => 'heroicon-o-clock',
                        'Closing' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Prospect' => 'primary',
                        'Follow up 1' => 'info',
                        'Follow up 2' => 'warning',
                        'Follow up 3' => 'warning',
                        'Closing' => 'success',
                        default => 'danger'
                    }),
                TextColumn::make('status_pembayaran')->label('Pembayaran')->badge()
                    ->icon(fn(string $state): ?string => match ($state) {
                        'Lunas' => 'heroicon-o-check-circle',
                        'Belum Lunas' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status_pekerjaan')->label('Pekerjaan')->badge()
                    ->icon(fn(string $state): ?string => match ($state) {
                        'Sekesai' => 'heroicon-o-check-circle',
                        'Belum Dikerjakan' => 'heroicon-o-minus-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Belum Dikerjakan' => 'gray',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Prospect' => 'Prospect',
                        'Follow up 1' => 'Follow up 1',
                        'Follow up 2' => 'Follow up 2',
                        'Follow up 3' => 'Follow up 3',
                        'Closing' => 'Closing'
                    ])
                    ->native(false),
                SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status_pembayaran')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                        'Belum Dibayar' => 'Belum Dibayar'
                    ])
                    ->native(false),
                SelectFilter::make('status_pekerjaan')
                    ->options([
                        'Selesai' => 'Selesai',
                        'Belum Dikerjakan' => 'Belum Dikerjakan',
                        'Dalam Proses' => 'Dalam Proses',
                    ])->native(false),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_informasi_masuk', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Belum Ada Proyek Pemetaan yang Pernah Dibuat')
            ->emptyStateDescription('Silahkan buat proyek pemetaan baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PersonelsRelationManager::class,
            StatusPembayaranRelationManager::class,
            DaftarAlatProjectRelationManager::class,
            StatusPekerjaanRelationManager::class,
            PengajuanDanasRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
