<?php

namespace App\Filament\Resources\SewaResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sewa;
use App\Filament\Traits\HasDateFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PesananSewa extends ChartWidget
{
    use HasDateFilter;

    protected static ?string $heading = 'Pesanan Sewa Harian';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $range = $this->getDateRange();
        $startDate = $range['start'];
        $endDate = $range['end'];

        // Query daily new rentals based on created_at
        $dailyRentals = Sewa::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare data for chart
        $labels = [];
        $data = [];

        // Generate all dates in range
        $period = Carbon::parse($startDate)->toPeriod($endDate);

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $rental = $dailyRentals->firstWhere('date', $dateString);
            $data[] = $rental ? $rental->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sewa Baru per Hari',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
