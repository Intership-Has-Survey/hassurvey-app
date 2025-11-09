<?php

namespace App\Filament\Resources\KalibrasiResource\Widgets;

use App\Models\Kalibrasi;
use App\Models\PengajuanDana;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
//
class KalibrasiOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $pemasukan = Kalibrasi::query()->pluck('harga')->sum();
        //where mengembalikan banyak data, makanya harus diawal, sum mengembalikan satu data
        $pengeluaran = PengajuanDana::where('pengajuanable_type', 'App\Models\Kalibrasi')->sum('dibayar');
        return [
            //paramter 2: 1 variable, 2 pemisah desimal, 3 pemisah ribuan 
            Stat::make('Pemasukan', 'Rp ' . number_format($pemasukan, 0, ',', '.')),
            Stat::make('Pengeluaran', 'Rp ' . number_format($pengeluaran, 0, ',', '.'))
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
