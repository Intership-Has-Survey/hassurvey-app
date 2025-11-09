<?php

namespace App\Filament\Resources\PengajuanDanaResource\Widgets;

use Filament\Forms;
use Filament\Forms\Form;
use Forms\Components\Grid;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Forms\Components\Select;
use App\Models\PengajuanDana;
use Filament\Forms\Components\Section;

use Filament\Support\Enums\IconPosition;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use App\Filament\Resources\PengajuanDanaResource\Pages\ListPengajuanDanas;

class PengajuanDanaOverview extends BaseWidget
{
    use InteractsWithPageTable;


    protected function getStats(): array
    {

        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        // jika semua data disum hasilnya 39510500.00
        // jika hanya data yang available disum hasilnya  5500000.00
        // secara default Eloquent mengambil data yang belum dihapus

        //mengambil semua data
        // $total = PengajuanDana::withTrashed()->sum('nilai');
        $pengeluaran = PengajuanDana::withTrashed()->sum('dibayar');

        $total = PengajuanDana::withTrashed()
            ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $endDate))
            ->sum('nilai');

        // dd($this->getPageTableQuery()->toSql());
        return [
            //
            // Stat::make('Total Pengajuan', 'Rp ' . $total),
            // Stat::make('Jumlah Dibayar', 'Rp ' . $pengeluaran),
            // Stat::make('Total Pengajuan', 'Rp ' . number_format($total, 0, ',', '.')),
            // Stat::make('Jumlah Dibayar', 'Rp ' . number_format($pengeluaran, 0, ',', '.')),
            Stat::make('Total Pengajuan',  'Rp ' . number_format($this->getPageTableQuery()->sum('nilai'), 0, ',', '.'))
                ->description('Total nilai yang diajukan')
                ->color('primary')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before),
            Stat::make('Total Dibayar', 'Rp ' . number_format($this->getPageTableQuery()->sum('dibayar'), 0, ',', '.'))
                ->description('Total yang dikeluarkan')
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before),

            // Stat::make('Total Products', $this->getPageTableRecords()->count()),
        ];
    }

    protected function getTablePage(): string
    {
        return ListPengajuanDanas::class;
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
