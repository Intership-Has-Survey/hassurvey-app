<?php

namespace App\Filament\Widgets;

use App\Models\Corporate;
use App\Models\Perorangan;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;

class GrafikCustomer extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?string $heading = 'Total Customer Kumulatif';

    protected static ?int $sort = 1;

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
                    'label' => 'Perorangan',
                    'data' => array_values($data['perorangan']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)',
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Perusahaan',
                    'data' => array_values($data['corporate']),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.3)',
                    'borderColor' => '#3B82F6',
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

        // Query dasar dengan filter tenant
        $peroranganQuery = Perorangan::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        $corporateQuery = Corporate::when($this->companyId, function ($query) {
            return $query->where('company_id', $this->companyId);
        });

        // 2. Hitung total customer SEBELUM rentang 12 bulan untuk nilai awal kumulatif
        $initialPeroranganCount = $peroranganQuery->clone()->where('created_at', '<', $startDate)->count();
        $initialCorporateCount = $corporateQuery->clone()->where('created_at', '<', $startDate)->count();

        // 3. Ambil data customer BARU per bulan HANYA dalam rentang 12 bulan
        $peroranganData = $peroranganQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $corporateData = $corporateQuery->clone()
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-01") as month'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Inisialisasi variabel untuk menyimpan hasil
        $cumulativeAllCustomers = [];
        $cumulativePeroranganCustomers = [];
        $cumulativeCorporateCustomers = [];

        // Mulai hitungan kumulatif dari total sebelum periode 12 bulan
        $cumulativePerorangan = $initialPeroranganCount;
        $cumulativeCorporate = $initialCorporateCount;
        $cumulativeAll = $initialPeroranganCount + $initialCorporateCount;

        // 4. Loop per bulan selama 12 bulan terakhir
        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $formattedMonth = $currentMonth->format('Y-m-01');

            // Ambil customer baru di bulan ini (jika ada)
            $newPerorangan = $peroranganData[$formattedMonth] ?? 0;
            $newCorporate = $corporateData[$formattedMonth] ?? 0;

            // Tambahkan customer baru ke total kumulatif
            $cumulativePerorangan += $newPerorangan;
            $cumulativeCorporate += $newCorporate;
            $cumulativeAll += ($newPerorangan + $newCorporate);

            // Simpan nilai kumulatif untuk bulan ini
            $cumulativePeroranganCustomers[$formattedMonth] = $cumulativePerorangan;
            $cumulativeCorporateCustomers[$formattedMonth] = $cumulativeCorporate;
            $cumulativeAllCustomers[$formattedMonth] = $cumulativeAll;

            $currentMonth->addMonth();
        }

        return [
            'all' => $cumulativeAllCustomers,
            'perorangan' => $cumulativePeroranganCustomers,
            'corporate' => $cumulativeCorporateCustomers,
        ];
    }

    protected function getOptions(): array
    {
        // Kode ini tidak perlu diubah, biarkan seperti aslinya.
        // ... (isi metode getOptions Anda yang sudah ada)
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

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
