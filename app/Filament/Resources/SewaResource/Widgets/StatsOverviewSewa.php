<?php

namespace App\Filament\Resources\SewaResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\PengajuanDana;
use App\Models\Sewa;
use App\Models\TransaksiPembayaran;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use App\Models\statusPembayaran;
use App\Models\pengajuanDanas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsOverviewSewa extends BaseWidget
{
    // Properti untuk menyimpan state filter tanggal range
    public ?string $dateRange = null;

    protected function getHeaderActions(): array
    {
        // Tombol filter dengan DateRangePicker dari package CodeWithKyrian
        return [
            Action::make('filter')
                ->label('Filter Tanggal')
                ->icon('heroicon-o-funnel')
                ->form([
                    DateRangePicker::make('dateRange')
                        ->label('Rentang Tanggal')
                        ->default($this->dateRange),
                ])
                ->action(function (array $data) {
                    $this->dateRange = $data['dateRange'];
                }),
        ];
    }

    protected function getStats(): array
    {
        $pendapatanQuery = StatusPembayaran::query()
            ->whereHasMorph('payable', [Sewa::class]);

        $pengeluaranQuery = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [PengajuanDana::class], function ($query) {
                $query->whereHas('sewa');
            });

        // Parse date range jika ada
        if ($this->dateRange) {
            [$startDate, $endDate] = explode(' - ', $this->dateRange);

            $pendapatanQuery->whereBetween('tanggal_pembayaran', [$startDate, $endDate]);
            $pengeluaranQuery->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        }

        // Kalkulasi data yang lebih akurat
        $pendapatan = $pendapatanQuery->sum('nilai');
        $pengeluaran = $pengeluaranQuery->sum('nilai');

        // Query untuk sewa stats
        $sewaQuery = Sewa::query();
        if ($this->dateRange) {
            [$startDate, $endDate] = explode(' - ', $this->dateRange);
            $sewaQuery->whereBetween('tanggal_informasi_masuk', [$startDate, $endDate]);
        }

        return [
            Stat::make('Pemasukan Sewa', 'Rp ' . number_format($pendapatan))
                ->description('Total pembayaran yang masuk dari status pembayaran')
                ->color('success')
                ->extraAttributes(['class' => 'col-span-1']),
            Stat::make('Pengeluaran Sewa', 'Rp ' . number_format($pengeluaran))
                ->description('Total transaksi pembayaran dari pengajuan dana')
                ->color('danger')
                ->extraAttributes(['class' => 'col-span-1']),
        ];
    }
}

