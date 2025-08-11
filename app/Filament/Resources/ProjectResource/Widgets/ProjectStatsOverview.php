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

        return [
            Stat::make('Jumlah Proyek (Sesuai Filter)', $query->clone()->count())
                ->description('Total proyek berdasarkan tab yang dipilih'),
            Stat::make('Pendapatan Proyek', 'Rp ' . number_format($pendapatan))
                ->description('Total pembayaran yang masuk dari status pembayaran')
                ->color('success'),
            Stat::make('Pengeluaran Proyek', 'Rp ' . number_format($pengeluaran))
                ->description('Total transaksi pembayaran dari pengajuan dana')
                ->color('danger'),
        ];
    }
}