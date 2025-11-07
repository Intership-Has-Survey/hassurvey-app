<?php

namespace App\Filament\Resources\PenjualanResource\Widgets;

use App\Models\Penjualan;
use App\Models\PengajuanDana;
use App\Models\StatusPembayaran;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PenjualanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // $pemasukan = Penjualan::query()->pluck('total_items')->sum();
        // $pemasukan = Penjualan::sum('total_items');
        $pemasukan = StatusPembayaran::where('payable_type', 'App\Models\Penjualan')->sum('nilai');
        $pengeluaran = PengajuanDana::where('pengajuanable_type', 'App\Models\Penjualan')->sum('dibayar');
        // @dump($pengeluaran);
        return [
            //
            Stat::make('Pemasukan', 'Rp ' . number_format($pemasukan, 0, ',', '.')),
            Stat::make('Pengeluaran', 'Rp ' . number_format($pengeluaran, 0, ',', '.')),
        ];
    }
}
