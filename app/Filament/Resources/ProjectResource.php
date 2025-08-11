<?php

namespace App\Filament\Resources;

use App\Models\Sales;
use App\Models\Project;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Actions;
use Filament\Forms\Form;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatusChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use App\Filament\Resources\ProjectResource\Widgets\ProjectsFilter;
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
                    TextInput::make('nama_project')
                        ->required()
                        ->label('Nama Proyek')
                        ->placeholder('Masukkan Nama Proyek'),
                    Select::make('status')
                        ->label('Status Proyek')
                        ->options([
                            'Prospect' => 'Prospect',
                            'Follow up 1' => 'Follow up 1',
                            'Follow up 2' => 'Follow up 2',
                            'Follow up 3' => 'Follow up 3',
                            'Closing' => 'Closing',
                            'Failed' => 'Failed',
                        ])
                        ->required()
                        ->default('Prospect')
                        ->native(false)
                        ->validationMessages([
                            'required' => 'Status proyek tidak boleh kosong',
                        ]),
                    Select::make('kategori_id')->relationship('kategori', 'nama')->searchable()->preload()
                        ->createOptionForm(self::getKategoriForm()),
                    Select::make('sales_id')
                        ->relationship('sales', 'nama', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                        ->label('Sales')
                        ->getOptionLabelFromRecordUsing(fn(Sales $record) => "{$record->nama} - {$record->nik}")
                        ->placeholder('Pilih sales')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(self::getSalesForm()),
                    DatePicker::make('tanggal_informasi_masuk')->label('Tanggal Informasi Masuk')->native(false)->required()->default(now())->validationMessages([
                        'required' => 'Tanggal tidak boleh kosong',
                    ]),
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
                ])
                ->columns(2)
                ->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),

            Section::make('Informasi Customer')
                ->schema(self::getCustomerForm())
                ->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),

            Section::make('Lokasi Proyek')->schema(self::getAddressFields())->columns(2)->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),
            Section::make('Keuangan')->schema([
                TextInput::make('nilai_project_awal')
                    ->label('Nilai Proyek')
                    ->numeric()
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live()
                    ->placeholder('Masukkan anggaran proyek')
                    ->disabled(fn(?Model $record, callable $get) => $record && $record->exists && $get('status') === 'Closing'),

                Placeholder::make('nilai_ppn_display')
                    ->label('Nilai PPN (12%)')
                    ->content(function (Get $get): string {
                        if ($get('dikenakan_ppn')) {
                            $nilai = (float) str_replace([','], '', $get('nilai_project_awal'));
                            $nilaiBulat = floor($nilai);
                            return 'Rp. ' . number_format($nilaiBulat * 0.12, 0, ',');
                        }
                        return 'Rp. 0';
                    }),
                Toggle::make('dikenakan_ppn')
                    ->label('Kenakan PPN (12%)')
                    ->live()
                    ->disabled(fn(callable $get) => $get('status') === 'Closing'),
                Placeholder::make('nilai_project')
                    ->label('Total Tagihan')
                    ->content(function (Get $get): string {
                        $nilai = (float) str_replace([','], '', $get('nilai_project_awal'));
                        $nilaiBulat = floor($nilai);
                        $total = $nilaiBulat;
                        if ($get('dikenakan_ppn')) {
                            $total = $nilaiBulat + ($nilaiBulat * 0.12);
                        }
                        return 'Rp. ' . number_format($total, 0, ',');
                    }),
            ])->columns(2)->disabled(fn(callable $get) => $get('status_pekerjaan') === 'Selesai'),


            Hidden::make('user_id')->default(auth()->id()),
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
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
                        'Prospect' => 'info',
                        'Follow up 1' => 'warning',
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
                TrashedFilter::make(),

            ])
            ->actions([
                EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
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

    public static function getHeaderWidgets(): array
    {
        return [
            ProjectStatsOverview::class,
            ProjectsFilter::class,
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
