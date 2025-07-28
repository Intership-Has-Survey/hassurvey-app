<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrafikPesananBulan extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Pesanan Per Bulan (1 Tahun Terakhir)';

    protected static ?int $sort = 4;

    protected function getFormSchema(): array
    {
        return [];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = $this->getChartData();

        return [
            'datasets' => [
                // [
                //     'label' => 'Semua',
                //     'data' => array_values($data['all']),
                //     'backgroundColor' => '#6B7280', // Gray 500
                //     'borderSkipped' => true,
                // ],
                [
                    'label' => 'Pemetaan',
                    'data' => array_values($data['Project']),
                    'backgroundColor' => '#10B981', // Green 500
                    'borderSkipped' => true,
                ],
                [
                    'label' => 'Perusahaan',
                    'data' => array_values($data['Sewa']),
                    'backgroundColor' => '#3B82F6', // Blue 
                    'borderSkipped' => true,
                ],
            ],
            'labels' => array_keys($data['all']),
        ];
    }

    protected function getChartData(): array
    {
        $endDate = now()->endOfMonth();
        $startDate = $endDate->copy()->subMonths(11)->startOfMonth();

        $allOrders = [];
        $SewaOrders = [];
        $ProjectOrders = [];
        $labels = [];

        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('n/y');
            $labels[] = $formattedMonth;
            $allOrders[$formattedMonth] = 0;
            $SewaOrders[$formattedMonth] = 0;
            $ProjectOrders[$formattedMonth] = 0;
            $currentMonth->addMonth();
        }

        $SewaData = Sewa::select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $ProjectData = Project::select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($labels as $label) {
            $SewaOrders[$label] = $SewaData[$label] ?? 0;
            $ProjectOrders[$label] = $ProjectData[$label] ?? 0;
            $allOrders[$label] = $SewaOrders[$label] + $ProjectOrders[$label];
        }

        return [
            'all' => $allOrders,
            'Sewa' => $SewaOrders,
            'Project' => $ProjectOrders,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getChartData();
        $maxAll = empty($data['all']) ? 0 : max(array_values($data['all']));
        $maxSewa = empty($data['Sewa']) ? 0 : max(array_values($data['Sewa']));
        $maxProject = empty($data['Project']) ? 0 : max(array_values($data['Project']));

        $overallMax = max($maxAll, $maxSewa, $maxProject);

        if ($overallMax == 0) {
            $overallMax = 10;
        }

        $stepSize = ceil($overallMax / 4);
        $yAxisMax = $stepSize;

        if ($yAxisMax < $overallMax) {
            $yAxisMax = $stepSize * (floor($overallMax / $stepSize) + 1);
        }

        if ($yAxisMax < 5 && $overallMax > 0) {
            $yAxisMax = 5;
            $stepSize = 2;
        } elseif ($yAxisMax == 0 && $overallMax == 0) {
            $yAxisMax = 10;
            $stepSize = 2;
        }

        return [
            'scales' => [
                'x' => [
                    'barPercentage' => 0.9,
                    'categoryPercentage' => 0.8,
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'max' => $yAxisMax,
                    'ticks' => [
                        'stepSize' => $stepSize,
                    ],
                ],
            ],
        ];
    }
}