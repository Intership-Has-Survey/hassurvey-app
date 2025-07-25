<?php

namespace App\Filament\Widgets;

use App\Models\Corporate;
use App\Models\Perorangan;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrafikCustomerBulan extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Customer Per Bulan (12 Bulan Terakhir)';

    protected static ?int $sort = 3;

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
                    'label' => 'Perorangan',
                    'data' => array_values($data['perorangan']),
                    'backgroundColor' => '#10B981', // Green 500
                    'borderSkipped' => true,
                ],
                [
                    'label' => 'Perusahaan',
                    'data' => array_values($data['corporate']),
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

        $allCustomers = [];
        $peroranganCustomers = [];
        $corporateCustomers = [];
        $labels = [];

        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('n/y');
            $labels[] = $formattedMonth;
            $allCustomers[$formattedMonth] = 0;
            $peroranganCustomers[$formattedMonth] = 0;
            $corporateCustomers[$formattedMonth] = 0;
            $currentMonth->addMonth();
        }

        $peroranganData = Perorangan::select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $corporateData = Corporate::select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($labels as $label) {
            $peroranganCustomers[$label] = $peroranganData[$label] ?? 0;
            $corporateCustomers[$label] = $corporateData[$label] ?? 0;
            $allCustomers[$label] = $peroranganCustomers[$label] + $corporateCustomers[$label];
        }

        return [
            'all' => $allCustomers,
            'perorangan' => $peroranganCustomers,
            'corporate' => $corporateCustomers,
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
                    'stacked'=> true,
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