<?php

namespace App\Filament\Resources\PemilikResource\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use App\Models\Pemilik;

class RingkasanHarga extends BaseWidget
{
    use InteractsWithPageFilters, InteractsWithRecord;

    protected function getFilters(): array
    {
        return [
            'startDate' => [
                'label' => 'Tanggal Mulai',
                'type' => 'date',
            ],
            'endDate' => [
                'label' => 'Tanggal Akhir',
                'type' => 'date',
            ],
        ];
    }

    protected function getStats(): array
    {
        $pemilik = $this->getRecord(); // 👈 3. Ambil record dengan cara ini

        if (!$pemilik) { // 👈 4. Lakukan pengecekan pada variabel lokal
            return [];
        }

        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = $pemilik->riwayatSewaAlat(); // 👈 5. Gunakan variabel lokal

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $totalKotor = $query->sum('biaya_sewa_alat_final');
        $totalInvestor = $query->sum('pendapataninv_final');
        $totalHas = $query->sum('pendapatanhas_final');

        return [
            Stat::make('Total Pendapatan Kotor', Number::currency($totalKotor, 'IDR'))
                ->color('primary'),
            Stat::make('Total Pendapatan Investor', Number::currency($totalInvestor, 'IDR'))
                ->color('success'),
            Stat::make('Total Pendapatan Has', Number::currency($totalHas, 'IDR'))
                ->color('warning'),
        ];
    }
}
