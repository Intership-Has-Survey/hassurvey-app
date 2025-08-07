<?php

namespace App\Filament\Resources\PemilikResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\RelationManagers\RelationManager;

class TransaksiPembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'statusPengeluarans';
    protected static ?string $title = 'Realisasi Pembayaran';

    public function form(Form $form): Form
    {
        $pengajuan = $this->ownerRecord;

        return $form
            ->schema([
                Select::make('bulan_pembayaran')
                    ->label('Bulan Pembayaran')
                    ->options([
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset calculated values when month changes
                        $set('total_tagihan', null);
                        $set('sisa_pembayaran', null);
                    }),

                Placeholder::make('periode_tagihan')
                    ->label('Periode Tagihan')
                    ->content(function (callable $get) {
                        $bulan = $get('bulan_pembayaran');
                        if (!$bulan) {
                            return 'Pilih bulan pembayaran terlebih dahulu';
                        }

                        // Calculate date range: 27th of previous month to 26th of selected month
                        $year = now()->year;
                        $bulanInt = (int) $bulan;
                        $startDate = now()->year($year)->month($bulanInt)->subMonth()->setDay(27);
                        $endDate = now()->year($year)->month($bulanInt)->setDay(26);

                        return $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                    })
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                Placeholder::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->content(function (callable $get, $livewire) use ($pengajuan) {
                        $bulan = $get('bulan_pembayaran');
                        if (!$bulan) {
                            return 'Pilih bulan pembayaran terlebih dahulu';
                        }

                        // Calculate date range: 27th of previous month to 26th of selected month
                        $year = now()->year;
                        $bulanInt = (int) $bulan;
                        $startDate = now()->year($year)->month($bulanInt)->subMonth()->setDay(27);
                        $endDate = now()->year($year)->month($bulanInt)->setDay(26);

                        // Get the pemilik (owner) record to calculate pendapatan investor
                        $pemilik = $pengajuan->user->pemilik ?? null;
                        $totalTagihan = 0;

                        if ($pemilik) {
                            // Calculate pendapatan investor for the specified date range
                            $totalTagihan = $pemilik->riwayatSewaAlat()
                                ->whereBetween('tgl_keluar', [$startDate, $endDate])
                                ->sum('pendapataninv_final');
                        }

                        return 'Rp ' . number_format($totalTagihan, 0, ',', '.');
                    })
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                Placeholder::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->content(function (callable $get, $livewire) use ($pengajuan) {
                        $bulan = $get('bulan_pembayaran');
                        if (!$bulan) {
                            return 'Pilih bulan pembayaran terlebih dahulu';
                        }

                        // Check if payment already exists for this month
                        $existingPayment = $pengajuan->statusPengeluarans()
                            ->where('bulan_pembayaran', $bulan)
                            ->first();

                        if ($existingPayment) {
                            $tanggalTransaksi = \Carbon\Carbon::parse($existingPayment->tanggal_transaksi);
                            return 'Sudah Dibayar pada tanggal ' . $tanggalTransaksi->format('d M Y') . ' dengan nilai Rp ' . number_format($existingPayment->nilai, 0, ',', '.');
                        }

                        // Check if today is on or after the 27th of the selected month
                        $bulanInt = (int) $bulan;
                        $paymentDate = now()->month($bulanInt)->setDay(27);
                        if (now()->gte($paymentDate)) {
                            return 'Belum Dibayar - Tombol Bayar Tersedia';
                        } else {
                            return 'Belum Dibayar - Tombol Bayar Akan Tersedia pada Tanggal 27 ' . now()->month($bulanInt)->format('F');
                        }
                    })
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp')
                    ->maxlength(20)
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->native(false)
                    ->default(now())
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                Select::make('metode_pembayaran')
                    ->options(['Transfer' => 'Transfer', 'Tunai' => 'Tunai'])
                    ->required()
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran')
                    ->visible(fn(callable $get) => filled($get('bulan_pembayaran'))),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->maxlength(500)
                    ->nullable(),

                Hidden::make('user_id')->default(auth()->id()),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columns([
                TextColumn::make('bulan_pembayaran')
                    ->label('Periode Tagihan')
                    ->formatStateUsing(function ($state, Model $record) {
                        if (!$record->bulan_pembayaran) {
                            return 'N/A';
                        }

                        // Get month name from bulan_pembayaran
                        $bulanNames = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ];

                        $bulanName = $bulanNames[$record->bulan_pembayaran] ?? 'N/A';

                        // Calculate date range: 27th of previous month to 26th of selected month
                        $year = now()->year;
                        $bulanInt = (int) $record->bulan_pembayaran;

                        try {
                            // Calculate date range: 27th of previous month to 26th of selected month
                            $startDate = \Carbon\Carbon::create($year, $bulanInt, 27)->subMonth();
                            $endDate = \Carbon\Carbon::create($year, $bulanInt, 26);

                            return $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . ' (' . $bulanName . ')';
                        } catch (\Exception $e) {
                            // Return just the month name if calculation fails
                            return $bulanName;
                        }
                    })
                    ->sortable(),
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('nilai')->money('IDR'),
                TextColumn::make('metode_pembayaran')->badge(),
                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('user.name')->label('Dibayar oleh'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
