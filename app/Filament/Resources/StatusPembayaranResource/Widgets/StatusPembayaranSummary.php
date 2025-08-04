<?php

// namespace App\Filament\Widgets;
namespace App\Filament\Resources\StatusPembayaranResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\StatusPembayaran;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatusPembayaranSummary extends StatsOverviewWidget
{

    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $start = session('filter_start_date');
        $end = session('filter_end_date');

        $query = StatusPembayaran::query();

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }


        $totalPembayaran = $query->sum('nilai');

        $range = match (true) {
            $start && $end => 'Periode: ' . date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end)),
            $start         => 'Dari: ' . date('d M Y', strtotime($start)),
            $end           => 'Sampai: ' . date('d M Y', strtotime($end)),
            default        => 'Tanpa filter tanggal',
        };

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPembayaran, 0, ',', '.'))
                ->description($range),
        ];
    }
}
