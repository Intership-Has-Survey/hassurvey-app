<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\PengajuanDana;
use App\Models\Project;
use App\Models\StatusPembayaran;
use App\Models\TransaksiPembayaran;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ProjectStatsOverview extends BaseWidget
{
    // Properti untuk menyimpan state filter tanggal range
    public ?string $dateRange = null;

    protected function getHeaderActions(): array
    {
        // Tombol filter dengan DateRangePicker dari package CodeWithKyrian
        return [
            // Action::make('filter')
            //     ->label('Filter Tanggal')
            //     ->icon('heroicon-o-funnel')
            //     ->form([
            //         DateRangePicker::make('dateRange')
            //             ->label('Rentang Tanggal')
            //             ->default($this->dateRange),
            //     ])
            //     ->action(function (array $data) {
            //         $this->dateRange = $data['dateRange'];
            //     }),
        ];
    }
    protected function getPageTableQuery(): Builder
    {
        // Return base query for projects
        return Project::query();
    }


    protected function getStats(): array
    {
        // Query untuk pendapatan dari status pembayaran yang masuk ke project
        $pendapatanQuery = StatusPembayaran::query()
            ->whereHasMorph('payable', [Project::class]);

        // Query untuk pengeluaran dari transaksi pembayaran dari pengajuan dana di project
        $pengeluaranQuery = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [PengajuanDana::class], function ($query) {
                $query->whereHas('project');
            });

        // Kalkulasi data yang lebih akurat
        $pendapatan = $pendapatanQuery->sum('nilai');
        $pengeluaran = $pengeluaranQuery->sum('nilai');

        $query = $this->getPageTableQuery();

        return [
            Stat::make('Jumlah Proyek (Sesuai Filter)', $query->clone()->count())
                ->description('Total proyek berdasarkan tab yang dipilih'),
            Stat::make('Pendapatan Proyek', 'Rp ' . number_format($pendapatan))
                ->description('Total pembayaran yang masuk dari status pembayaran')
                ->color('success'),
            Stat::make('Pengeluaran Proyek', 'Rp ' . number_format($pengeluaran))
                ->description('Total transaksi pembayaran dari pengajuan dana')
                ->color('danger'),
        ];
    }
}
