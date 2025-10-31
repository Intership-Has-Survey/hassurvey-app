<?php

namespace App\Filament\Resources;

use App\Models\Sewa;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Support\RawJs;
use Ramsey\Uuid\Type\Integer;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use function Livewire\Volt\placeholder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SewaResource\Pages;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SewaResource\Pages\EditSewa;
use App\Filament\Resources\SewaResource\Pages\ListSewa;
use App\Filament\Resources\SewaResource\Pages\CreateSewa;
use App\Filament\Resources\SewaResource\Widgets\SewaFilter;
use Filament\Tables\Actions\BulkAction; // Tambahkan ini di atas
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Filament\Notifications\Notification; // Tambahkan ini di atas
use App\Filament\Resources\SewaResource\Widgets\StatsOverviewSewa;
use Illuminate\Database\Eloquent\Collection; // Tambahkan ini di atas
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\StatusPembyaranRelationManager;


class SewaResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Sewa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $navigationLabel = 'Sewa';
    protected static ?string $title = 'Penyewaan';
    protected static ?string $modelLabel = 'Penyewaan';
    protected static ?string $pluralModelLabel = 'Penyewaan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $calculateRentang = function (Set $set, Get $get) {
            $startDate = $get('tgl_mulai');
            $endDate = $get('tgl_selesai');

            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);

                if ($end->isBefore($start)) {
                    $set('rentang', 'Tanggal tidak valid');
                    return;
                }

                if ($start->isSameDay($end)) {
                    $set('rentang', '1 Hari');
                    return;
                }

                $diff = $start->diff($end);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $weeks = floor($days / 7);
                $remainingDays = $days % 7;

                $parts = [];
                if ($years > 0)
                    $parts[] = $years . ' Tahun';
                if ($months > 0)
                    $parts[] = $months . ' Bulan';
                if ($weeks > 0)
                    $parts[] = $weeks . ' Minggu';
                if ($remainingDays > 0)
                    $parts[] = $remainingDays . ' Hari';

                $rentangText = implode(' ', $parts);
                $set('rentang', !empty($rentangText) ? $rentangText : 'Durasi tidak valid');
            } else {
                $set('rentang', null);
            }
        };

        return $form
            ->schema([
                Section::make('Informasi Kontrak')
                    ->schema([
                        TextInput::make('kode_sewa')
                            ->label('Kode Sewa')
                            ->disabled() // biar user tidak bisa ubah manual
                            ->dehydrated(false) // jangan simpan input dari user
                            ->visibleOn(['edit', 'view']),
                        TextInput::make('judul')
                            ->required()
                            ->placeholder('Masukkan Judul Penyewaan')
                            ->label('Judul Penyewaan')
                            ->columnSpanFull(),
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
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_mulai')
                                    ->required()
                                    ->label('Tanggal Mulai')
                                    ->live(onBlur: true)
                                    ->native(false)
                                    ->withoutTime()
                                    ->validationMessages([
                                        'required' => 'Tanggal mulai wajib diisi',
                                    ])
                                    ->default(today())
                                    ->afterStateUpdated($calculateRentang),
                                DatePicker::make('tgl_selesai')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Tanggal Selesai wajib diisi',
                                    ])
                                    ->label('Tanggal Selesai')
                                    ->minDate(fn(Get $get) => $get('tgl_mulai'))
                                    ->live(onBlur: true)
                                    ->native(false)
                                    ->placeholder('dd/mm/yyyy')
                                    ->afterStateUpdated($calculateRentang),
                            ]),
                        Placeholder::make('rentang')
                            ->label('Durasi Waktu Sewa')
                            ->dehydrated()
                            ->content(function (Get $get): string|HtmlString {
                                $rentangValue = $get('rentang');
                                if ($rentangValue) {
                                    return $rentangValue;
                                }
                                return new HtmlString('<i>(Terhitung otomatis setelah tanggal dipilih)</i>');
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Lokasi Project Alat yang Disewa')
                    ->schema(self::getAddressFields())->columns(2),

                Section::make('Informasi Customer')
                    ->schema(self::getCustomerForm()),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

                Section::make('Informasi Harga Penyewaan')
                    ->schema([
                        Placeholder::make('info_harga')
                            ->visibleOn('create')
                            ->label('')
                            ->content('Informasi Harga akan muncul ketika kontrak sudah dibuat dan alat sudah ditambahkan.'),

                        Placeholder::make('harga_perkiraan')
                            ->label('Harga Total Perkiraan (Total Nilai Kontrak)')
                            ->hiddenOn('create')
                            ->content(function (Get $get, ?Sewa $record): string|HtmlString {
                                $tglSelesaiKontrak = $get('tgl_selesai');
                                if ($record && $tglSelesaiKontrak) {
                                    $tglSelesaiKontrak = Carbon::parse($tglSelesaiKontrak);
                                    $totalPerkiraan = 0;
                                    foreach ($record->daftarAlat as $alat) {
                                        $pivotData = $alat->pivot;
                                        if ($pivotData && $pivotData->tgl_keluar && $pivotData->harga_perhari) {
                                            $tglKeluarAlat = Carbon::parse($pivotData->tgl_keluar);
                                            if ($tglSelesaiKontrak->gte($tglKeluarAlat)) {
                                                $durasiPerkiraan = $tglKeluarAlat->diffInDays($tglSelesaiKontrak) + 1;
                                                $totalPerkiraan += $durasiPerkiraan * $pivotData->harga_perhari;
                                            }
                                        }
                                    }
                                    if ($totalPerkiraan > 0) {
                                        return 'Rp ' . number_format($totalPerkiraan, 0, ',', '.');
                                    }
                                }
                                return new HtmlString('<i>Tambahkan Alat terlebih dahulu</i>');
                            }),

                        Placeholder::make('harga_real')
                            ->label('Harga Total Alat yang sudah dikembalikan')
                            ->hiddenOn('create')
                            ->content(function (?Sewa $record): string|HtmlString {
                                if ($record) {
                                    $total = $record->daftarAlat()
                                        ->wherePivotNotNull('tgl_masuk')
                                        ->sum('riwayat_sewa.biaya_sewa_alat');
                                    if ($total > 0) {
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    }
                                }
                                return new HtmlString('<i>Belum ada alat yang dikembalikan</i>');
                            }),

                        TextInput::make('harga_fix')
                            ->label('Harga Final (Harga Setelah Negosiasi)')
                            ->hiddenOn('create')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->placeholder('Masukkan harga akhir setelah negosiasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->live(),

                        Toggle::make('is_locked')
                            ->label('Tutup dan Kunci Transaksi Sewa')
                            ->helperText('Aktifkan untuk menyelesaikan sewa. Data tidak akan bisa diubah lagi.')
                            ->visible(function (Get $get, ?Sewa $record): bool {
                                // Hanya tampil jika harga fix sudah diisi DAN sewa belum terkunci
                                return filled($get('harga_fix')) && !$record?->is_locked && $record->daftarAlat()->exists() && !$record->daftarAlat()
                                    ->whereNull('tgl_masuk')
                                    ->exists();;
                            })
                    ])->columns(1),
                Hidden::make('user_id')->default(auth()->id()),
            ])
            ->disabled(fn(?Sewa $record): bool => $record?->is_locked ?? false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_sewa')->sortable()->searchable()->wrap(),
                TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                TextColumn::make('customer_display')
                    ->label('Klien Utama')
                    ->state(function (Sewa $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        return $record->perorangan->first()?->nama ?? 'HAS Survey';
                    })
                    // ->searchable(),
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->formatStateUsing(fn($state) => $state ?? 'HAS SURVEY'),
                TextColumn::make('tgl_mulai')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('tgl_selesai')
                    ->date('d-m-Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status Sewa')
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Konfirmasi Selesai' => 'info',
                        'Jatuh Tempo' => 'danger',
                        'Belum Selesai' => 'warning',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make()
                // ->visible(fn(Sewa $record): bool => !$record->is_locked),
                // DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),

                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (Collection $records, BulkAction $action) {
                            foreach ($records as $record) {
                                if ($record->daftarAlat()->exists()) {
                                    Notification::make()
                                        ->title('Gagal Menghapus')
                                        ->body("Sewa '{$record->judul}' tidak dapat dihapus karena sudah memiliki riwayat penyewaan alat.")
                                        ->danger()
                                        ->send();
                                    $action->halt();
                                }
                            }
                        }),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kontrak Sewa yang Pernah Dibuat')
            ->emptyStateDescription('Silahkan buat penyewaan baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RiwayatSewasRelationManager::class,
            StatusPembyaranRelationManager::class,
            PengajuanDanasRelationManager::class,
            ActivitylogRelationManager::class,

        ];
    }

    // public function getWidgets(): array
    // {
    //     return [
    //         StatsOverviewSewa::class,
    //     ];
    // }

    public static function getHeaderWidgets(): array
    {
        return [
            StatsOverviewSewa::class,
            SewaFilter::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSewa::route('/'),
            'create' => Pages\CreateSewa::route('/create'),
            'edit' => Pages\EditSewa::route('/{record}/edit'),
            'view' => Pages\ViewSewa::route('/{record}'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed()
            ->where(function (Builder $query) {
                // Tampilkan sewa yang bukan dari project
                $query->whereDoesntHave('projects')
                    // Atau sewa dari project yang sudah memiliki alat
                    ->orWhereHas('projects', function (Builder $projectQuery) {
                        $projectQuery->whereHas('daftarAlat');
                    });
            });
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
