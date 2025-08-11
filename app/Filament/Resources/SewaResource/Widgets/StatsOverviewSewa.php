<?php

namespace App\Filament\Resources\SewaResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
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

class StatsOverviewSewa extends BaseWidget
{
    // Properti untuk menyimpan state filter tanggal range
    public ?string $dateRange = null;

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

    protected function getHeaderActions(): array
    {
        // Tombol filter dengan DateRangePicker dari package CodeWithKyrian
        return [
            Action::make('filter')
                ->label('Filter Tanggal')
                ->icon('heroicon-o-funnel')
                ->form([
                    DateRangePicker::make('dateRange')
                        ->label('Rentang Tanggal')
                        ->default($this->dateRange),
                ])
                ->action(function (array $data) {
                    $this->dateRange = $data['dateRange'];
                }),
        ];
    }

    protected function getStats(): array
    {
        // Query untuk Pemasukan
        $pendapatanQuery = StatusPembayaran::query()
            ->where('company_id', $this->companyId)
            ->whereHasMorph('payable', [Sewa::class]);

        // Query untuk Pengeluaran
        $pengeluaranQuery = TransaksiPembayaran::query()
            ->where('company_id', $this->companyId)
            ->whereHasMorph('payable', [PengajuanDana::class], function ($query) {
                $query->whereHas('sewa');
            });

        // ADDED: Query untuk menghitung total sewa
        // Pastikan model Sewa juga memiliki relasi dengan company
        $sewaQuery = Sewa::query()->where('company_id', $this->companyId);

        // Terapkan filter rentang tanggal jika ada
        if ($this->dateRange) {
            // Pecah string tanggal menjadi tanggal awal dan akhir
            [$startDate, $endDate] = explode(' - ', $this->dateRange);

            // Terapkan filter `whereBetween` pada semua query
            $pendapatanQuery->whereBetween('tanggal_pembayaran', [$startDate, $endDate]);
            $pengeluaranQuery->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            // Filter sewa berdasarkan tanggal informasi masuk
            $sewaQuery->whereBetween('tanggal_informasi_masuk', [$startDate, $endDate]);
        }

        // Kalkulasi total
        $pendapatan = $pendapatanQuery->sum('nilai');
        $pengeluaran = $pengeluaranQuery->sum('nilai');
        $totalSewa = $sewaQuery->count(); // Hitung jumlah sewa

        return [
            Stat::make('Pemasukan Sewa', 'Rp ' . number_format($pendapatan))
                ->description('Total pembayaran sewa yang diterima')
                ->color('success'),
            Stat::make('Pengeluaran Sewa', 'Rp ' . number_format($pengeluaran))
                ->description('Total pengeluaran terkait sewa')
                ->color('danger'),
            // ADDED: Stat baru untuk menampilkan total sewa
            Stat::make('Total Proses Sewa', $totalSewa)
                ->description('Jumlah proses sewa dalam periode waktu yang dipilih')
                ->color('info'),
        ];
    }
}
