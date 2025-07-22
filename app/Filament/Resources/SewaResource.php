<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Sewa;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use function Livewire\Volt\placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\SewaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SewaResource\RelationManagers;
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
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->placeholder('Masukkan Judul Penyewaan')
                            ->label('Judul Penyewaan')
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('tgl_mulai')
                                    ->required()
                                    ->label('Tanggal Mulai')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated($calculateRentang),
                                Forms\Components\DatePicker::make('tgl_selesai')
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
                        Select::make('customer_type')->label('Tipe Customer')->options([Perorangan::class => 'Perorangan', Corporate::class => 'Corporate'])->live()->required()->placeholder('Pilih tipe customer terlebih dahulu'),
                        Select::make('customer_id')->label('Pilih Customer')->placeholder('Pilih customer')->options(fn(Get $get): array => $get('customer_type') ? $get('customer_type')::pluck('nama', 'id')->all() : [])->searchable()->required()->createOptionForm(fn(Get $get) => [])->createOptionUsing(fn(array $data, Get $get): ?string => null)->visible(fn(Get $get) => filled($get('customer_type'))),
                    ]),

                Section::make('Informasi Harga Penyewaan')
                    ->schema([
                        Forms\Components\Placeholder::make('info_harga')
                            ->visibleOn('create')
                            ->label('')
                            ->content('Informasi Harga akan muncul ketika kontrak sudah dibuat dan alat sudah ditambahkan.'),

                        Forms\Components\Placeholder::make('harga_perkiraan')
                            ->label('Harga Total Perkiraan (Total Nilai Kontrak)')
                            ->visibleOn('edit')
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

                        Forms\Components\Placeholder::make('harga_real')
                            ->label('Harga Total Alat yang sudah dikembalikan')
                            ->visibleOn('edit')
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

                        Forms\Components\TextInput::make('harga_fix')
                            ->label('Harga Fix (Harga Setelah Negosiasi)')
                            ->visibleOn('edit')
                            ->placeholder('Masukkan harga akhir setelah negosiasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->live(),

                        Toggle::make('tutup_sewa')
                            ->label('Tutup dan Kunci Transaksi Sewa')
                            ->helperText('Aktifkan untuk menyelesaikan sewa. Data tidak akan bisa diubah lagi.')
                            ->visible(fn(Get $get): bool => filled($get('harga_fix')))
                    ])->columns(1),
            ])
            ->disabled(fn(?Sewa $record): bool => $record?->is_locked ?? false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
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
                Tables\Actions\EditAction::make()
                    ->visible(fn(Sewa $record): bool => !$record->is_locked),

                Tables\Actions\Action::make('selesaikan_sewa')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
}
