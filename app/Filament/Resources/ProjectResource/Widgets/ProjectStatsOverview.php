<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\PengajuanDana;
use App\Models\Project;
use App\Models\StatusPembayaran;
use App\Models\TransaksiPembayaran;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProjectStatsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    public ?array $filters = [];
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

    protected function getTablePage(): string
    {
        return ListProjects::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        // Apply filters from the page
        foreach ($this->filters ?? [] as $filterName => $filterValue) {
            if ($filterValue === null) {
                continue;
            }

            switch ($filterName) {
                case 'status':
                case 'status_pembayaran':
                case 'status_pekerjaan':
                    $query->where($filterName, $filterValue);
                    break;
                case 'kategori':
                    $query->whereHas('kategori', fn($q) => $q->where('id', $filterValue));
                    break;
                case 'created_at':
                    if (isset($filterValue['start'], $filterValue['end'])) {
                        $query->whereBetween('created_at', [
                            $filterValue['start'],
                            $filterValue['end'],
                        ]);
                    }
                    break;
            }
        }

        $projectIds = $query->clone()->pluck('id');

        $pendapatan = StatusPembayaran::query()
            ->whereIn('payable_id', $projectIds)
            ->where('payable_type', Project::class)
            ->sum('nilai');

        $pengeluaran = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [PengajuanDana::class], function (Builder $q) use ($projectIds) {
                $q->whereHas('project', function (Builder $q2) use ($projectIds) {
                    $q2->whereIn('id', $projectIds);
                });
            })
            ->sum('nilai');

        $totalTagihan = StatusPembayaran::query()
            ->whereIn('payable_id', $projectIds)
            ->where('payable_type', Project::class)
            ->sum('nilai');

        $totalDibayar = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [Project::class], function (Builder $q) use ($projectIds) {
                $q->whereIn('id', $projectIds);
            })
            ->sum('nilai');

        $belumDibayar = $totalTagihan - $totalDibayar;

        $closing = $query->clone()->where('status', 'Closing')->count();
        $prospect = $query->clone()->where('status', 'Prospect')->count();
        $follow1 = $query->clone()->where('status', 'Follow up 1')->count();
        $follow2 = $query->clone()->where('status', 'Follow up 2')->count();
        $follow3 = $query->clone()->where('status', 'Follow up 3')->count();
        $failed  = $query->clone()->where('status', 'Failed')->count();

        return [
            // Stat::make('Jumlah Proyek', $query->clone()->count())
            //     ->description('Total proyek sesuai filter'),

            Stat::make('Closing', $closing)->color('success'),
            Stat::make('Prospect', $prospect)->color('info'),
            Stat::make('Follow Up 1', $follow1)->color('warning'),
            Stat::make('Follow Up 2', $follow2)->color('warning'),
            Stat::make('Follow Up 3', $follow3)->color('warning'),
            Stat::make('Failed', $failed)->color('danger'),

            Stat::make('Pendapatan Proyek', 'Rp ' . number_format($pendapatan, 0, ',', '.'))
                ->description('Total pendapatan')
                ->color('success'),

            Stat::make('Pengeluaran Proyek', 'Rp ' . number_format($pengeluaran, 0, ',', '.'))
                ->description('Total pengeluaran')
                ->color('danger'),

            Stat::make('Belum Dibayar', 'Rp ' . number_format(max($belumDibayar, 0), 0, ',', '.'))
                ->description('Sisa tagihan yang belum dibayar')
                ->color('warning'),
        ];
    }
}
