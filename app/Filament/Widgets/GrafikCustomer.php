<?php

namespace App\Filament\Widgets;

use App\Models\Corporate;
use App\Models\Perorangan;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrafikCustomer extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Total Customer Kumulatif Per Tahun';

    protected static ?int $sort = 1;

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
                    'label' => 'Perorangan',
                    'data' => array_values($data['perorangan']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)', // Green 500 with 30% opacity
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Perusahaan',
                    'data' => array_values($data['corporate']),
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
        $earliestPerorangan = Perorangan::min('created_at');
        $earliestCorporate = Corporate::min('created_at');

        $earliestDate = null;

        if ($earliestPerorangan && $earliestCorporate) {
            $earliestDate = min($earliestPerorangan, $earliestCorporate);
        } elseif ($earliestPerorangan) {
            $earliestDate = $earliestPerorangan;
        } elseif ($earliestCorporate) {
            $earliestDate = $earliestCorporate;
        }

        if (is_null($earliestDate)) {
            // If no data, default to the last 12 months for a meaningful chart
            $startDate = now()->subMonths(11)->startOfMonth();
        } else {
            $startDate = Carbon::parse($earliestDate)->startOfMonth();
        }

        $endDate = now()->endOfMonth();

        $allCustomers = [];
        $peroranganCustomers = [];
        $corporateCustomers = [];
        $labels = [];

        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('Y-m-01'); // Use Y-m-01 for time scale
            $labels[] = $formattedMonth;
            $allCustomers[$formattedMonth] = 0;
            $peroranganCustomers[$formattedMonth] = 0;
            $corporateCustomers[$formattedMonth] = 0;
            $currentMonth->addMonth();
        }

        $peroranganData = Perorangan::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $corporateData = Corporate::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($labels as $label) {
            $peroranganCustomers[$label] = $peroranganData[$label] ?? 0;
            $corporateCustomers[$label] = $corporateData[$label] ?? 0;
            $allCustomers[$label] = $peroranganCustomers[$label] + $corporateCustomers[$label];
        }

        $cumulativePerorangan = 0;
        $cumulativeCorporate = 0;
        $cumulativeAll = 0;

        $cumulativePeroranganCustomers = [];
        $cumulativeCorporateCustomers = [];
        $cumulativeAllCustomers = [];

        foreach ($labels as $label) {
            $cumulativePerorangan += $peroranganCustomers[$label];
            $cumulativeCorporate += $corporateCustomers[$label];
            $cumulativeAll += $allCustomers[$label];

            $cumulativePeroranganCustomers[$label] = $cumulativePerorangan;
            $cumulativeCorporateCustomers[$label] = $cumulativeCorporate;
            $cumulativeAllCustomers[$label] = $cumulativeAll;
        }

        return [
            'all' => $cumulativeAllCustomers,
            'perorangan' => $cumulativePeroranganCustomers,
            'corporate' => $cumulativeCorporateCustomers,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getChartData();
        $maxAll = empty($data['all']) ? 0 : max(array_values($data['all']));
        $maxPerorangan = empty($data['perorangan']) ? 0 : max(array_values($data['perorangan']));
        $maxCorporate = empty($data['corporate']) ? 0 : max(array_values($data['corporate']));

        $overallMax = max($maxAll, $maxPerorangan, $maxCorporate);

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
                        'tooltipFormat' => 'MMM YYYY',
                        'displayFormats' => [
                            'month' => 'MMM YYYY',
                            'year' => 'YYYY',
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