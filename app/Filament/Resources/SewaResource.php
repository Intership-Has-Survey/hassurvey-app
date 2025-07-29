<?php

namespace App\Filament\Resources;

use App\Models\Sewa;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Support\RawJs;
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
use Filament\Tables\Columns\TextColumn;
use Ramsey\Uuid\Type\Integer;
use function Livewire\Volt\placeholder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SewaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SewaResource\Pages\EditSewa;
use App\Filament\Resources\SewaResource\Pages\ListSewa;
use App\Filament\Resources\SewaResource\Pages\CreateSewa;
use App\Filament\Resources\SewaResource\RelationManagers;
use Filament\Tables\Actions\BulkAction; // Tambahkan ini di atas
use Filament\Notifications\Notification; // Tambahkan ini di atas
use Illuminate\Database\Eloquent\Collection; // Tambahkan ini di atas
use App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\PengajuanDanasRelationManager;

class SewaResource extends Resource
{
    protected static ?string $model = Sewa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $navigationLabel = 'Layanan Sewa';
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
                        TextInput::make('judul')
                            ->required()
                            ->placeholder('Masukkan Judul Penyewaan')
                            ->label('Judul Penyewaan')
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_mulai')
                                    ->required()
                                    ->label('Tanggal Mulai')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated($calculateRentang),
                                DatePicker::make('tgl_selesai')
                                    ->required()
                                    ->label('Tanggal Selesai')
                                    ->minDate(fn(Get $get) => $get('tgl_mulai'))
                                    ->live(onBlur: true)
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
                    ->schema([
                        Select::make('provinsi')->label('Provinsi')->required()->placeholder('Pilih Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null) && $set('kecamatan', null) && $set('desa', null)),
                        Select::make('kota')->label('Kota/Kabupaten')->required()->placeholder('Pilih Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null) && $set('desa', null)),
                        Select::make('kecamatan')->label('Kecamatan')->required()->placeholder('Pilih Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                        Select::make('desa')->label('Desa/Kelurahan')->required()->placeholder('Pilih Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                        Textarea::make('detail_alamat')->label('Detail Alamat')->required()->columnSpanFull()->placeholder('cth: Jl. Supriyadi No,12, RT.3/RW.4'),
                    ])->columns(2),

                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_flow_type')
                            ->label('Tipe Customer')
                            ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                            ->live()->required()->dehydrated(false)
                            ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null)),


                        Select::make('corporate_id')
                            ->relationship('corporate', 'nama')
                            ->label('Pilih Perusahaan')
                            ->live()
                            ->createOptionForm(self::getCorporateForm())
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
                                    ->searchable()->required()
                                    ->createOptionForm(self::getPeroranganForm()) // Asumsikan Anda punya helper method ini
                                    ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id),
                            ])
                            ->minItems(1)
                            ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                            ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                            ->addActionLabel('Tambah PIC')
                            ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                            ->saveRelationshipsUsing(function (Model $record, array $state): void {
                                $ids = array_map(fn($item) => $item['perorangan_id'], $state);
                                $record->perorangan()->sync($ids);

                                if ($record->corporate_id) {
                                    $corporate = $record->corporate;
                                    foreach ($ids as $peroranganId) {
                                        if (!$corporate->perorangan()->wherePivot('perorangan_id', $peroranganId)->exists()) {
                                            $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                                        }
                                    }
                                }
                            }),
                    ]),

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
                            ->maxlength(20)
                            ->placeholder('Masukkan harga akhir setelah negosiasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->live(),

                        Toggle::make('tutup_sewa')
                            ->label('Tutup dan Kunci Transaksi Sewa')
                            ->helperText('Aktifkan untuk menyelesaikan sewa. Data tidak akan bisa diubah lagi.')
                            ->visible(function (Get $get, ?Sewa $record): bool {
                                // Hanya tampil jika harga fix sudah diisi DAN sewa belum terkunci
                                return filled($get('harga_fix')) && !$record?->is_locked;
                            })
                    ])->columns(1),
            ])
            ->disabled(fn(?Sewa $record): bool => $record?->is_locked ?? false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                TextColumn::make('perorangan.nama')
                    ->label('PIC/Customer')
                    ->searchable(),
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
                EditAction::make()
                    ->visible(fn(Sewa $record): bool => !$record->is_locked),
                Action::make('selesaikan_sewa')
                    ->label('Selesaikan Sewa')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan dan Kunci Transaksi?')
                    ->modalDescription('Aksi ini tidak dapat dibatalkan. Pastikan semua proses sudah final.')
                    ->action(fn(Sewa $record) => $record->update(['is_locked' => true]))
                    ->visible(fn(Sewa $record): bool => $record->status === 'Konfirmasi Selesai' || $record->status === 'Jatuh Tempo'),
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
            PengajuanDanasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSewa::route('/'),
            'create' => Pages\CreateSewa::route('/create'),
            'edit' => Pages\EditSewa::route('/{record}/edit'),
        ];
    }


    private static function getPeroranganForm(): array
    {
        return [
            Section::make('Informasi Personal')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('NIK')
                        ->length(16)
                        ->numeric()
                        ->unique(ignoreRecord: true),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }
    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->required()
                        ->maxLength(200),
                    TextInput::make('npwp')
                        ->label('NPWP')
                        ->maxLength(20),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }
    private static function getAddressFields(): array
    {
        return [
            Select::make('provinsi')
                ->label('Provinsi')
                ->required()
                ->placeholder('Pilih provinsi')
                ->options(TrefRegion::query()
                    ->where(DB::raw('LENGTH(code)'), 2)
                    ->pluck('name', 'code'))
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('kota', null);
                    $set('kecamatan', null);
                    $set('desa', null);
                }),

            Select::make('kota')
                ->label('Kota/Kabupaten')
                ->required()
                ->placeholder('Pilih kota/kabupaten')
                ->options(function (Get $get) {
                    $provinceCode = $get('provinsi');
                    if (!$provinceCode) return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $provinceCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 5)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('kecamatan', null);
                    $set('desa', null);
                }),

            Select::make('kecamatan')
                ->label('Kecamatan')
                ->required()
                ->placeholder('Pilih kecamatan')
                ->options(function (Get $get) {
                    $regencyCode = $get('kota');
                    if (!$regencyCode) return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $regencyCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 8)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('desa', null);
                }),

            Select::make('desa')
                ->label('Desa/Kelurahan')
                ->required()
                ->placeholder('Pilih desa/kelurahan')
                ->options(function (Get $get) {
                    $districtCode = $get('kecamatan');
                    if (!$districtCode) return [];

                    return TrefRegion::query()
                        ->where('code', 'like', $districtCode . '.%')
                        ->where(DB::raw('LENGTH(code)'), 13)
                        ->pluck('name', 'code');
                })
                ->live()
                ->searchable(),

            Textarea::make('detail_alamat')
                ->required()
                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}
