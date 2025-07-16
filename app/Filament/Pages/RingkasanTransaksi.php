<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\PengajuanDana;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\TransaksiPembayaranResource;

class RingkasanTransaksi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Transaksi Keluar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $title = 'Ringkasan Pembayaran';
    protected static string $view = 'filament.pages.ringkasan-transaksi';
    protected static ?int $navigationSort = 3;


    public function table(Table $table): Table
    {
        return $table
            ->query(PengajuanDana::query()->whereHas('transaksiPembayarans'))
            ->columns([
                TextColumn::make('judul_pengajuan')
                    ->label('Judul Pengajuan')
                    ->searchable()
                    ->sortable(),

                // Menghitung total nilai yang diajukan
                TextColumn::make('total_pengajuan')
                    ->label('Total Diajukan')
                    ->state(function (PengajuanDana $record): float {
                        return $record->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
                    })
                    ->money('IDR'),

                // Menghitung total yang sudah dibayarkan
                TextColumn::make('total_dibayarkan')
                    ->label('Total Dibayarkan')
                    ->state(function (PengajuanDana $record): float {
                        return $record->transaksiPembayarans()->sum('nilai');
                    })
                    ->money('IDR'),

                // TextColumn::make('status')
                //     ->badge()
                //     ->color(fn(string $state): string => match ($state) {
                //         'Belum Lunas' => 'danger',
                //         'Lunas' => 'success',
                //         default => 'gray',
                //     }),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->state(function (PengajuanDana $record): string {
                        $totalDiajukan = $record->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
                        $totalDibayar = $record->transaksiPembayarans()->sum('nilai');

                        if ($totalDiajukan > 0 && $totalDibayar >= $totalDiajukan) {
                            return 'Lunas';
                        }
                        return 'Belum Lunas';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'warning',
                    }),
            ])
            ->actions([
                Action::make('view_transactions')
                    ->label('Lihat Transaksi')
                    ->icon('heroicon-o-eye')
                    // Mengarahkan ke halaman daftar transaksi dengan filter
                    ->url(fn(PengajuanDana $record): string => TransaksiPembayaranResource::getUrl('index', [
                        'pengajuan_dana_id' => $record->id,
                    ])),
            ]);
    }
}
