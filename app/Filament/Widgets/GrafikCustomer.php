<?php

namespace App\Filament\Widgets;

use App\Models\Corporate;
use App\Models\Perorangan;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrafikCustomer extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Total customers';

    protected static ?int $sort = 2;

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
                    'label' => 'Corporate',
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

        $startDate = Carbon::parse($earliestDate)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Ensure we only go back 12 months from the end date
        if ($startDate->diffInMonths($endDate) >= 12) {
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
        }

        $allCustomers = [];
        $peroranganCustomers = [];
        $corporateCustomers = [];
        $labels = [];

        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('M Y');
            $labels[] = $formattedMonth;
            $allCustomers[$formattedMonth] = 0;
            $peroranganCustomers[$formattedMonth] = 0;
            $corporateCustomers[$formattedMonth] = 0;
            $currentMonth->addMonth();
        }

        $peroranganData = Perorangan::select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $corporateData = Corporate::select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'), DB::raw('count(*) as total'))
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


}
