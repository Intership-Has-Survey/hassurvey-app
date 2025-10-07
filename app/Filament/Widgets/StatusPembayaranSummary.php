<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\StatusPembayaran;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Facades\Filament;

class StatusPembayaranSummary extends StatsOverviewWidget
{
    public ?string $companyId;

    public function mount(): void
    {
        // Cek apakah ada tenant yang aktif, lalu ambil ID-nya.
        if ($tenant = Filament::getTenant()) {
            $this->companyId = $tenant->id;
        } else {
            $this->companyId = null;
        }
    }

    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $start = session('filter_start_date');
        $end = session('filter_end_date');

        $query = StatusPembayaran::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }


        $totalPembayaran = $query->sum('nilai');

        $range = match (true) {
            $start && $end => 'Periode: ' . date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end)),
            $start => 'Dari: ' . date('d M Y', strtotime($start)),
            $end => 'Sampai: ' . date('d M Y', strtotime($end)),
            default => 'Tanpa filter tanggal',
        };

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPembayaran, 0, ',', '.'))
                ->description($range),
        ];
    }

    // protected function getStats(): array
    // {
    //     $startDate = !is_null($this->filters['startDate'] ?? null) ?
    //         Carbon::parse($this->filters['startDate']) :
    //         null;

    //     $endDate = !is_null($this->filters['endDate'] ?? null) ?
    //         Carbon::parse($this->filters['endDate']) :
    //         null;

    //     $serviceType = $this->filters['serviceType'] ?? 'Semua';

    //     $formatNumber = function (int $number): string {
    //         if ($number < 1000) {
    //             return (string) Number::format($number, 0);
    //         }

    //         if ($number < 1000000) {
    //             return Number::format($number / 1000, 2) . 'k';
    //         }

    //         return Number::format($number / 1000000, 2) . 'm';
    //     };

    //     $pendapatanBersih = 0;
    //     $customerBaru = 0;
    //     $pesananBaru = 0;

    //     // Set default startDate and endDate if null
    //     if (is_null($startDate)) {
    //         $earliestProjectDate = Project::min('tanggal_informasi_masuk');
    //         $earliestSewaDate = Sewa::min('tgl_mulai');
    //         $startDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');
    //     }
    //     if (is_null($endDate)) {
    //         $endDate = now();
    //     }

    //     if ($serviceType === 'Layanan Pemetaan') {
    //         $pesananBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])->count();
    //         $customerBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])
    //             ->distinct('corporate_id')
    //             ->count('corporate_id');
    //     } elseif ($serviceType === 'Layanan Sewa') {
    //         $pesananBaru = Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])->count();
    //         $customerBaru = Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])
    //             ->distinct('corporate_id')
    //             ->count('corporate_id');
    //     } elseif ($serviceType === 'Semua') {
    //         $pesananBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])->count()
    //             + Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])->count();
    //         $customerBaru = Project::whereBetween('tanggal_informasi_masuk', [$startDate, $endDate])
    //             ->distinct('corporate_id')
    //             ->count('corporate_id')
    //             + Sewa::whereBetween('tgl_mulai', [$startDate, $endDate])
    //             ->distinct('corporate_id')
    //             ->count('corporate_id');
    //     } else {
    //         // For other services, show zero or coming soon
    //         $pesananBaru = 0;
    //         $customerBaru = 0;
    //     }

    //     return [
    //         Stat::make('Pendapatan Bersih', 'Rp' . $formatNumber($pendapatanBersih))
    //             ->description('Coming Soon')
    //             ->color('secondary'),
    //         Stat::make('Customer Baru', $formatNumber($customerBaru))
    //             ->description('Customer Baru dalam rentang filter')
    //             ->color('primary'),
    //         Stat::make('Pesanan Baru', $formatNumber($pesananBaru))
    //             ->description('Pesanan Baru dalam rentang filter')
    //             ->color('success'),
    //     ];
    // }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
