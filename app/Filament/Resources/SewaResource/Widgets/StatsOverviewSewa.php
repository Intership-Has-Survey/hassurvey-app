<?php

namespace App\Filament\Resources\SewaResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Resources\SewaResource\Pages\ListSewa;
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
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsOverviewSewa extends BaseWidget
{
    use InteractsWithPageTable;
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

        $sewaIds = $query->clone()->pluck('id');

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
}
