<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrafikPesanan extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Total Pesanan Kumulatif';

    protected static ?int $sort = 3;

    protected function getFormSchema(): array
    {
        return [];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = $this->getChartData();

        return [
            'datasets' => [
                [
                    'label' => 'Semua',
                    'data' => array_values($data['all']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(107, 114, 128, 0.3)', // Gray 500 with 30% opacity
                    'borderColor' => '#6B7280', // Solid line
                ],
                [
                    'label' => 'Project',
                    'data' => array_values($data['Project']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)', // Green 500 with 30% opacity
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Sewa',
                    'data' => array_values($data['Sewa']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.3)', // Blue 500 with 30% opacity
                    'borderColor' => '#3B82F6',
                ],
            ],
            'labels' => array_keys($data['all']),
        ];
    }

    protected function getChartData(): array
    {
        $earliestProject = Project::min('created_at');
        $earliestSewa = Sewa::min('created_at');

        $earliestDate = null;

        if ($earliestProject && $earliestSewa) {
            $earliestDate = min($earliestProject, $earliestSewa);
        } elseif ($earliestProject) {
            $earliestDate = $earliestProject;
        } elseif ($earliestSewa) {
            $earliestDate = $earliestSewa;
        }

        if (is_null($earliestDate)) {
            // If no data, default to the last 12 months for a meaningful chart
            $startDate = now()->subMonths(11)->startOfMonth();
        } else {
            $startDate = Carbon::parse($earliestDate)->startOfMonth();
        }

        $endDate = now()->endOfMonth();

        $allOrders = [];
        $ProjectOrders = [];
        $SewaOrders = [];
        $labels = [];

        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('Y-m-01'); // Use Y-m-01 for time scale
            $labels[] = $formattedMonth;
            $allOrders[$formattedMonth] = 0;
            $ProjectOrders[$formattedMonth] = 0;
            $SewaOrders[$formattedMonth] = 0;
            $currentMonth->addMonth();
        }

        $ProjectData = Project::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $SewaData = Sewa::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($labels as $label) {
            $ProjectOrders[$label] = $ProjectData[$label] ?? 0;
            $SewaOrders[$label] = $SewaData[$label] ?? 0;
            $allOrders[$label] = $ProjectOrders[$label] + $SewaOrders[$label];
        }

        $cumulativeProject = 0;
        $cumulativeSewa = 0;
        $cumulativeAll = 0;

        $cumulativeProjectOrders = [];
        $cumulativeSewaOrders = [];
        $cumulativeAllOrders = [];

        foreach ($labels as $label) {
            $cumulativeProject += $ProjectOrders[$label];
            $cumulativeSewa += $SewaOrders[$label];
            $cumulativeAll += $allOrders[$label];

            $cumulativeProjectOrders[$label] = $cumulativeProject;
            $cumulativeSewaOrders[$label] = $cumulativeSewa;
            $cumulativeAllOrders[$label] = $cumulativeAll;
        }

        return [
            'all' => $cumulativeAllOrders,
            'Project' => $cumulativeProjectOrders,
            'Sewa' => $cumulativeSewaOrders,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getChartData();
        $maxAll = empty($data['all']) ? 0 : max(array_values($data['all']));
        $maxProject = empty($data['Project']) ? 0 : max(array_values($data['Project']));
        $maxSewa = empty($data['Sewa']) ? 0 : max(array_values($data['Sewa']));

        $overallMax = max($maxAll, $maxProject, $maxSewa);

        if ($overallMax == 0) {
            $overallMax = 10;
        }

        $stepSize = ceil($overallMax / 4);
        $yAxisMax = $stepSize * 4;

        if ($yAxisMax < $overallMax) {
            $yAxisMax = $stepSize * (floor($overallMax / $stepSize) + 1);
        }

        if ($yAxisMax < 10 && $overallMax > 0) {
            $yAxisMax = 10;
            $stepSize = 2;
        } elseif ($yAxisMax == 0 && $overallMax == 0) {
            $yAxisMax = 10;
            $stepSize = 2;
        }

        return [
            'scales' => [
                'x' => [
                    'type' => 'time',
                    'time' => [
                        'unit' => 'month',
                        'tooltipFormat' => 'MMM yyyy',
                        'displayFormats' => [
                            'month' => 'MMM yyyy',
                            'year' => 'yyyy',
                        ],
                    ],
                    'ticks' => [
                        'source' => 'auto',
                        'autoSkip' => true,
                        'maxRotation' => 0,
                        'minRotation' => 0,
                        'callback' => 'function(value, index, values) {
                            if (index === 0 || index === values.length - 1) {
                                return this.getLabelForValue(value);
                            }
                            return "";
                        }',
                    ],
                ],
                'y' => [
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