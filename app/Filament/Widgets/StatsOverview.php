<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Sewa;
use App\Models\Project;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // $startDate = !is_null($this->filters['startDate'] ?? null) ?
        //     Carbon::parse($this->filters['startDate']) :
        //     null;
        // $endDate = !is_null($this->filters['endDate'] ?? null) ?
        //     Carbon::parse($this->filters['endDate']) :
        //     null;
        // $serviceType = $this->filters['serviceType'] ?? 'Semua';
        $startDate = $this->filters['created_at']['start'] ?? null;
        $endDate = $this->filters['created_at']['end'] ?? null;
        $serviceType = $this->filters['serviceType'] ?? 'Semua';

        $formatNumber = function (int $number): string {
            if ($number < 1000) return (string) Number::format($number, 0);
            if ($number < 1000000) return Number::format($number / 1000, 2) . 'k';
            return Number::format($number / 1000000, 2) . 'm';
        };

        // $pendapatanBersih = 0;
        // $customerBaru = 0;
        // $pesananBaru = 0;

        // // Set default startDate and endDate if null
        // if (is_null($startDate)) {
        //     $earliestProjectDate = Project::min('tanggal_informasi_masuk');
        //     $earliestSewaDate = Sewa::min('tgl_mulai');
        //     $startDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');
        // }
        // if (is_null($endDate)) {
        //     $endDate = now();
        // }
        $projectsQuery = Project::query()
            ->when($startDate, fn($query) => $query->whereDate('tanggal_informasi_masuk', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tanggal_informasi_masuk', '<=', $endDate));

        $sewasQuery = Sewa::query()
            ->when($startDate, fn($query) => $query->whereDate('tgl_mulai', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tgl_mulai', '<=', $endDate));

        $pesananBaru = 0;
        $customerBaru = 0;
        $pendapatanBersih = 0;

        if ($serviceType === 'Layanan Pemetaan' || $serviceType === 'Semua') {
            $pesananBaru += $projectsQuery->clone()->count();
            $projectCorporateIds = $projectsQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualProjectIds = $projectsQuery->clone()->whereNull('corporate_id')->pluck('id');
            $projectPeroranganIds = DB::table('project_perorangan')
                ->whereIn('project_id', $individualProjectIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Layanan Sewa' || $serviceType === 'Semua') {
            $pesananBaru += $sewasQuery->clone()->count();
            $sewaCorporateIds = $sewasQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualSewaIds = $sewasQuery->clone()->whereNull('corporate_id')->pluck('id');
            $sewaPeroranganIds = DB::table('sewa_perorangan')
                ->whereIn('sewa_id', $individualSewaIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Semua') {
            $allCorporateIds = ($projectCorporateIds ?? collect())->merge($sewaCorporateIds ?? collect())->unique();
            $allPeroranganIds = ($projectPeroranganIds ?? collect())->merge($sewaPeroranganIds ?? collect())->unique();
            $customerBaru = $allCorporateIds->count() + $allPeroranganIds->count();
        } elseif ($serviceType === 'Layanan Pemetaan') {
            $customerBaru = ($projectCorporateIds ?? collect())->unique()->count() + ($projectPeroranganIds ?? collect())->unique()->count();
        } elseif ($serviceType === 'Layanan Sewa') {
            $customerBaru = ($sewaCorporateIds ?? collect())->unique()->count() + ($sewaPeroranganIds ?? collect())->unique()->count();
        }

        return [
            Stat::make('Pendapatan Bersih', 'Rp' . $formatNumber($pendapatanBersih))
                ->description('Coming Soon')
                ->color('secondary'),
            Stat::make('Total Customer', $formatNumber($customerBaru))
                ->description('Customer baru dalam rentang waktu')
                ->color('primary'),
            Stat::make('Total Pesanan', $formatNumber($pesananBaru))
                ->description('Total pesanan dalam rentang waktu')
                ->color('success'),
        ];
    }
}
