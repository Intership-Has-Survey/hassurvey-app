<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Support\Number;
use Carbon\Carbon;

class RingkasanPemetaan extends BaseWidget
{
    use InteractsWithRecord;

    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false; // biar langsung load, bukan async

    protected function getStats(): array
    {
        $project = $this->getRecord();

        if (!$project) {
            return [];
        }

        $pengeluaranQuery = $project->pengajuanDanas();
        $pembayaranQuery = $project->statusPembayaran();
        $alatQuery = $project->daftarAlat();
        $personelQuery = $project->personels();
        $pembayaranPersonelQuery = $project->pembayaranPersonel();

        $pengeluaran = $pengeluaranQuery->whereNotIn('status', [0, 3])->sum('nilai');
        $pembayaran = $pembayaranQuery->sum('nilai');
        $totalAlat = $alatQuery->count();
        $totalPersonel = $personelQuery->count();
        $pembayaranPersonel = $pembayaranPersonelQuery->sum('nilai');


        return [
            Stat::make('Pengeluaran', Number::currency($pengeluaran, 'IDR'))
                ->color('danger'),
            Stat::make('Pembayaran', Number::currency($pembayaran, 'IDR'))
                ->color('success'),
            Stat::make('Realisasi Pembayaran ke Personel', Number::currency($pembayaranPersonel, 'IDR'))
                ->color('warning'),
            Stat::make('Total Alat yang digunakan', $totalAlat)
                ->color('info'),
            Stat::make('Total Personel', $totalPersonel)
                ->color('info'),
        ];
    }
}
