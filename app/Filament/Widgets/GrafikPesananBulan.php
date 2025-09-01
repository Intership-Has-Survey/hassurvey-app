<?php

namespace App\Filament\Widgets;

use App\Models\Kalibrasi;
use App\Models\Penjualan;
use App\Models\Project;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;

class GrafikPesananBulan extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Pesanan Baru';

    protected static ?int $sort = 4;

    public ?string $companyId; // Pastikan properti ini ada

    public function mount(): void
    {
        // Cek apakah ada tenant yang aktif, lalu ambil ID-nya.
        if ($tenant = Filament::getTenant()) {
            $this->companyId = $tenant->id;
        } else {
            $this->companyId = null;
        }
    }

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
                    'label' => 'Sewa',
                    'data' => array_values($data['Sewa']),
                    'backgroundColor' => '#3B82F6', // Blue 
                    'borderSkipped' => true,
                ],
                [
                    'label' => 'Servis & Kalibrasi',
                    'data' => array_values($data['Kalibrasi']),
                    'backgroundColor' => '#f34141ff', // Blue 
                    'borderSkipped' => true,
                ],
                [
                    'label' => 'Penjualan',
                    'data' => array_values($data['Penjualan']),
                    'backgroundColor' => '#a5a313ff', // Blue 
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

        $projectQuery = Project::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        $sewaQuery = Sewa::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        $kalibrasiQuery = Kalibrasi::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        $penjualanQuery = Penjualan::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        $allOrders = [];
        $SewaOrders = [];
        $ProjectOrders = [];
        $KalibrasiOrders = [];
        $PenjualanOrders = [];
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

        $SewaData = $sewaQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $ProjectData = $projectQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $KalibrasiData = $kalibrasiQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $PenjualanData = $penjualanQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%c/%y") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($labels as $label) {
            $SewaOrders[$label] = $SewaData[$label] ?? 0;
            $ProjectOrders[$label] = $ProjectData[$label] ?? 0;
            $KalibrasiOrders[$label] = $KalibrasiData[$label] ?? 0;
            $PenjualanOrders[$label] = $PenjualanData[$label] ?? 0;
            $allOrders[$label] = $SewaOrders[$label] + $ProjectOrders[$label] + $KalibrasiOrders[$label] + $PenjualanOrders[$label];
        }

        return [
            'all' => $allOrders,
            'Sewa' => $SewaOrders,
            'Project' => $ProjectOrders,
            'Kalibrasi' => $KalibrasiOrders,
            'Penjualan' => $PenjualanOrders,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getChartData();
        $maxAll = empty($data['all']) ? 0 : max(array_values($data['all']));
        $maxSewa = empty($data['Sewa']) ? 0 : max(array_values($data['Sewa']));
        $maxProject = empty($data['Project']) ? 0 : max(array_values($data['Project']));
        $maxKalibrasi = empty($data['Kalibrasi']) ? 0 : max(array_values($data['Kalibrasi']));
        $maxPenjualan = empty($data['Penjualan']) ? 0 : max(array_values($data['Penjualan']));

        $overallMax = max($maxAll, $maxSewa, $maxProject, $maxKalibrasi, $maxPenjualan);

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