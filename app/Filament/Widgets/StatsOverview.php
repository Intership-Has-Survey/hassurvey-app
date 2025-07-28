<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use App\Models\Project;
use App\Models\Sewa;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            null;

        $serviceType = $this->filters['serviceType'] ?? 'Semua';

        $formatNumber = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }

            return Number::format($number / 1000000, 2) . 'm';
        };

        $pendapatanBersih = 0;
        $customerBaru = 0;
        $pesananBaru = 0;

        // Set default startDate and endDate if null
        if (is_null($startDate)) {
            $earliestProjectDate = Project::min('tanggal_informasi_masuk');
            $earliestSewaDate = Sewa::min('tgl_mulai');
            $startDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');
        }
        if (is_null($endDate)) {
            $endDate = now();
        }

        if ($serviceType === 'Layanan Pemetaan') {
            $pesananBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])->count();
            $customerBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])
                ->distinct('corporate_id')
                ->count('corporate_id');
        } elseif ($serviceType === 'Layanan Sewa') {
            $pesananBaru = Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])->count();
            $customerBaru = Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])
                ->distinct('corporate_id')
                ->count('corporate_id');
        } elseif ($serviceType === 'Semua') {
            $pesananBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])->count()
                + Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])->count();
            $customerBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])
                ->distinct('corporate_id')
                ->count('corporate_id')
                + Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])
                    ->distinct('corporate_id')
                    ->count('corporate_id');
        } else {
            // For other services, show zero or coming soon
            $pesananBaru = 0;
            $customerBaru = 0;
        }

        return [
            Stat::make('Pendapatan Bersih', 'Rp' . $formatNumber($pendapatanBersih))
                ->description('Coming Soon')
                ->color('secondary'),
            Stat::make('Customer Baru', $formatNumber($customerBaru))
                ->description('Unique customers in timeframe')
                ->color('primary'),
            Stat::make('Pesanan Baru', $formatNumber($pesananBaru))
                ->description('New orders in timeframe')
                ->color('success'),
        ];
    }
}
