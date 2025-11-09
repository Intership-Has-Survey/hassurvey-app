<?php

namespace App\Filament\Resources\SewaResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Resources\SewaResource\Pages\ListSewa;
use App\Filament\Resources\SewaResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\PengajuanDana;
use App\Models\Sewa;
use App\Models\TransaksiPembayaran;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use App\Models\statusPembayaran;
use App\Models\pengajuanDanas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsOverviewSewa extends BaseWidget
{
    // Properti untuk menyimpan state filter tanggal range
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
        return ListSewa::class;
    }

    protected function getPageTableQuery(): ?Builder
    {
        try {
            // Get the table query directly from the resource without using the trait
            $resource = app(SewaResource::class);
            $query = $resource::getEloquentQuery();

            // Apply company scope if tenant is active
            if ($this->companyId) {
                $query->where('company_id', $this->companyId);
            }

            return $query;
        } catch (\Exception $e) {
            // If there's any error getting the table query, return null
            report($e);
            return null;
        }
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        // If no query is available, return empty stats
        if (!$query) {
            return $this->getEmptyStats();
        }

        // Apply filters from the page
        foreach ($this->filters ?? [] as $filterName => $filterValue) {
            if ($filterValue === null) {
                continue;
            }

            switch ($filterName) {
                case 'status':
                case 'status_pembayaran':
                    $query->where($filterName, $filterValue);
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

        // Safely get sewa IDs
        $sewaIds = $query->clone()->pluck('id')->toArray();

        // If no sewa IDs found, return empty stats
        if (empty($sewaIds)) {
            return $this->getEmptyStats();
        }

        $pendapatan = StatusPembayaran::query()
            ->whereIn('payable_id', $sewaIds)
            ->where('payable_type', Sewa::class)
            ->sum('nilai');

        $pengeluaran = TransaksiPembayaran::query()
            ->whereHasMorph('payable', [PengajuanDana::class], function (Builder $q) use ($sewaIds) {
                $q->whereHas('Sewa', function (Builder $q2) use ($sewaIds) {
                    $q2->whereIn('id', $sewaIds);
                });
            })
            ->sum('nilai');

        return [
            Stat::make('Jumla Penyewaan (Sesuai Filter)', $query->clone()->count())
                ->description('Jumlah proses sewa dalam periode waktu yang dipilih')
                ->color('info'),
            Stat::make('Pemasukan Sewa', 'Rp ' . number_format($pendapatan))
                ->description('Total pembayaran sewa yang diterima')
                ->color('success'),
            Stat::make('Pengeluaran Sewa', 'Rp ' . number_format($pengeluaran))
                ->description('Total pengeluaran terkait sewa')
                ->color('danger'),
        ];
    }
    protected function getEmptyStats(): array
    {
        return [
            Stat::make('Jumla Penyewaan (Sesuai Filter)', 0)
                ->description('Tidak ada data sewa')
                ->color('gray'),
            Stat::make('Pemasukan Sewa', 'Rp 0')
                ->description('Total pembayaran sewa yang diterima')
                ->color('gray'),
            Stat::make('Pengeluaran Sewa', 'Rp 0')
                ->description('Total pengeluaran terkait sewa')
                ->color('gray'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
