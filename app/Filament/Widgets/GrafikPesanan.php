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

class GrafikPesanan extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Total Pesanan Kumulatif';
    protected static ?int $sort = 3;
    public ?string $companyId;

    public function mount(): void
    {
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
                    'backgroundColor' => 'rgba(107, 114, 128, 0.3)',
                    'borderColor' => '#6B7280',
                ],
                [
                    'label' => 'Project',
                    'data' => array_values($data['Project']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)',
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Sewa',
                    'data' => array_values($data['Sewa']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.3)',
                    'borderColor' => '#3B82F6',
                ],
                [
                    'label' => 'Kalibrasi',
                    'data' => array_values($data['Kalibrasi']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(248, 48, 48, 0.3)',
                    'borderColor' => '#f34141ff',
                ],
                [
                    'label' => 'Penjualan',
                    'data' => array_values($data['Penjualan']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(133, 160, 12, 0.3)',
                    'borderColor' => '#a5a313ff',
                ],
            ],
            'labels' => array_keys($data['all']),
        ];
    }

    /**
     * Ganti metode ini dengan kode yang baru.
     */
    protected function getChartData(): array
    {
        // 1. Tetapkan rentang tanggal untuk 12 bulan terakhir
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Query dasar dengan filter tenant untuk setiap model
        $projectQuery = Project::when($this->companyId, fn($query) => $query->where('company_id', $this->companyId));
        $sewaQuery = Sewa::when($this->companyId, fn($query) => $query->where('company_id', $this->companyId));
        $kalibrasiQuery = Kalibrasi::when($this->companyId, fn($query) => $query->where('company_id', $this->companyId));
        $penjualanQuery = Penjualan::when($this->companyId, fn($query) => $query->where('company_id', $this->companyId));

        // 2. Hitung total pesanan SEBELUM rentang 12 bulan untuk nilai awal kumulatif
        $initialProjectCount = $projectQuery->clone()->where('created_at', '<', $startDate)->count();
        $initialSewaCount = $sewaQuery->clone()->where('created_at', '<', $startDate)->count();
        $initialKalibrasiCount = $kalibrasiQuery->clone()->where('created_at', '<', $startDate)->count();
        $initialPenjualanCount = $penjualanQuery->clone()->where('created_at', '<', $startDate)->count();

        // 3. Ambil data pesanan BARU per bulan HANYA dalam rentang 12 bulan
        $getMonthlyData = function ($query) use ($startDate, $endDate) {
            return $query->clone()
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        };

        $projectData = $getMonthlyData($projectQuery);
        $sewaData = $getMonthlyData($sewaQuery);
        $kalibrasiData = $getMonthlyData($kalibrasiQuery);
        $penjualanData = $getMonthlyData($penjualanQuery);

        // Inisialisasi variabel hasil dan nilai kumulatif awal
        $results = [
            'all' => [],
            'Project' => [],
            'Sewa' => [],
            'Kalibrasi' => [],
            'Penjualan' => [],
        ];
        $cumulative = [
            'Project' => $initialProjectCount,
            'Sewa' => $initialSewaCount,
            'Kalibrasi' => $initialKalibrasiCount,
            'Penjualan' => $initialPenjualanCount,
            'all' => $initialProjectCount + $initialSewaCount + $initialKalibrasiCount + $initialPenjualanCount,
        ];

        // 4. Loop per bulan selama 12 bulan terakhir
        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('Y-m-01');

            // Tambahkan pesanan baru ke total kumulatif
            $cumulative['Project'] += $projectData[$formattedMonth] ?? 0;
            $cumulative['Sewa'] += $sewaData[$formattedMonth] ?? 0;
            $cumulative['Kalibrasi'] += $kalibrasiData[$formattedMonth] ?? 0;
            $cumulative['Penjualan'] += $penjualanData[$formattedMonth] ?? 0;
            $cumulative['all'] = $cumulative['Project'] + $cumulative['Sewa'] + $cumulative['Kalibrasi'] + $cumulative['Penjualan'];

            // Simpan nilai kumulatif untuk bulan ini
            foreach ($results as $key => &$value) {
                $results[$key][$formattedMonth] = $cumulative[$key];
            }

            $currentMonth->addMonth();
        }

        return $results;
    }

    protected function getOptions(): array
    {
        // Kode ini tidak perlu diubah, biarkan seperti aslinya.
        // ... (isi metode getOptions Anda yang sudah ada)
        $data = $this->getChartData();
        $maxAll = empty($data['all']) ? 0 : max(array_values($data['all']));
        $maxProject = empty($data['Project']) ? 0 : max(array_values($data['Project']));
        $maxSewa = empty($data['Sewa']) ? 0 : max(array_values($data['Sewa']));
        $maxKalibrasi = empty($data['Kalibrasi']) ? 0 : max(array_values($data['Kalibrasi']));
        $maxPenjualan = empty($data['Penjualan']) ? 0 : max(array_values($data['Penjualan']));

        $overallMax = max($maxAll, $maxProject, $maxSewa, $maxKalibrasi, $maxPenjualan);

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