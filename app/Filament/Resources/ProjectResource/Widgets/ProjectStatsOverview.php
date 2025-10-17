<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Flowframe\Trend\Trend;
use Filament\Actions\Action;
use App\Models\PengajuanDana;
use filament\Facades\Filament;
use Flowframe\Trend\TrendValue;
use App\Models\StatusPembayaran;
use App\Models\PembayaranPersonel;
use App\Models\TransaksiPembayaran;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

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

        $pengeluaranDua = TransaksiPembayaran::where('company_id', $this->companyId)
            ->where('payable_type', PembayaranPersonel::class)
            ->sum('nilai');

        $pengeluaranTiga = PengajuanDana::where('company_id', $this->companyId)
            ->where('pengajuanable_type', Project::class)
            ->sum('dibayar');

        $totalTagihan = Project::where('company_id', $this->companyId)->sum('nilai_project');

        $totalDibayar = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [Project::class], function (Builder $q) use ($projectIds) {
                $q->whereIn('id', $projectIds);
            })
            ->sum('nilai');

        $belumDibayar = $totalTagihan - $pendapatan;

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
                ->description('Pembayaran yang sudah diterima, bukan nilai proyek')
                ->color('success'),

            Stat::make('Pengeluaran Proyek', 'Rp ' . number_format($pengeluaranDua + $pengeluaranTiga, 0, ',', '.'))
                ->description('Pengeluaran untuk pengajuan dana dan pembayaran personel')
                ->color('danger'),

            Stat::make('Belum Dibayar', 'Rp ' . number_format($belumDibayar, 0, ',', '.'))
                ->description('Total nilai project - total pendapatan proyek')
                ->color('warning')
                ->extraAttributes([
                    'title' => 'Total dari semua nilai proyek yang belum dibayar',
                ])
        ];
    }
}
