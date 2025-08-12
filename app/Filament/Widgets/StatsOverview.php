<?php

namespace App\Filament\Widgets;

use App\Models\Kalibrasi;
use App\Models\Penjualan;
use Carbon\Carbon;
use App\Models\Sewa;
use App\Models\Project;
use App\Models\StatusPembayaran;
use App\Models\PengajuanDana;
use Faker\Core\Uuid;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Helpers\TenantHelper;
use Filament\Facades\Filament;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $this->companyId = $tenant?->id;

        if (!$this->companyId) {
            return [
                Stat::make('Pendapatan Bersih', 'Rp0')->description('Tenant tidak ditemukan')->color('secondary'),
                Stat::make('Total Customer', '0')->color('primary')->description('Tenant tidak ditemukan'),
                Stat::make('Total Pesanan', '0')->color('success')->description('Tenant tidak ditemukan'),
            ];
        }

        $startDate = $this->filters['created_at']['start'] ?? null;
        $endDate = $this->filters['created_at']['end'] ?? null;
        $serviceType = $this->filters['serviceType'] ?? 'Semua';

        $startDate ??= Carbon::parse('2000-01-01');
        $endDate ??= now();

        $formatNumber = function (float|int $number): string {
            if ($number < 1000)
                return (string) Number::format($number, 0);
            if ($number < 1000000)
                return Number::format($number / 1000, 2) . 'k';
            return Number::format($number / 1000000, 2) . 'm';
        };

        $pendapatanMasuk = StatusPembayaran::where('company_id', $this->companyId)
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('nilai');

        $pengeluaran = PengajuanDana::where('company_id', $this->companyId)
            ->where('dalam_review', 'approved')
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('nilai');

        $pendapatanBersih = $pendapatanMasuk - $pengeluaran;

        $projectsQuery = Project::where('company_id', $this->companyId)
            ->when($startDate, fn($query) => $query->whereDate('tanggal_informasi_masuk', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tanggal_informasi_masuk', '<=', $endDate));

        $sewasQuery = Sewa::where('company_id', $this->companyId)
            ->when($startDate, fn($query) => $query->whereDate('tgl_mulai', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tgl_mulai', '<=', $endDate));

        $kalibrasisQuery = Kalibrasi::where('company_id', $this->companyId)
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate));

        $penjualansQuery = Penjualan::where('company_id', $this->companyId)
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate));
        $pesananBaru = 0;
        $customerBaru = 0;

        if ($serviceType === 'Layanan Servis dan Kalibrasi' || $serviceType === 'Semua') {
            $pesananBaru += $kalibrasisQuery->clone()->count();
            $kalibrasiCorporateIds = $kalibrasisQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualkalibrasiIds = $kalibrasisQuery->clone()->whereNull('corporate_id')->pluck('id');
            $kalibrasiPeroranganIds = DB::table('kalibrasi_perorangan')
                ->whereIn('kalibrasi_id', $individualkalibrasiIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Layanan Penjualan Alat' || $serviceType === 'Semua') {
            $pesananBaru += $penjualansQuery->clone()->count();
            $penjualanCorporateIds = $penjualansQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualpenjualanIds = $penjualansQuery->clone()->whereNull('corporate_id')->pluck('id');
            $penjualanPeroranganIds = DB::table('penjualan_perorangan')
                ->whereIn('penjualan_id', $individualpenjualanIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Layanan Pemetaan' || $serviceType === 'Semua') {
            $pesananBaru += $projectsQuery->clone()->count();
            $projectCorporateIds = $projectsQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualProjectIds = $projectsQuery->clone()->whereNull('corporate_id')->pluck('id');
            $projectPeroranganIds = DB::table('project_perorangan')
                ->whereIn('project_id', $individualProjectIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Layanan Sewa' || $serviceType === 'Semua') {
            $pesananBaru += $sewasQuery->clone()->count();
            $sewaCorporateIds = $sewasQuery->clone()->whereNotNull('corporate_id')->pluck('corporate_id');
            $individualSewaIds = $sewasQuery->clone()->whereNull('corporate_id')->pluck('id');
            $sewaPeroranganIds = DB::table('sewa_perorangan')
                ->whereIn('sewa_id', $individualSewaIds)
                ->distinct()
                ->pluck('perorangan_id');
        }
        if ($serviceType === 'Semua') {
            $allCorporateIds = ($projectCorporateIds ?? collect())
                ->merge($sewaCorporateIds ?? collect())
                ->merge($kalibrasiCorporateIds ?? collect())
                ->merge($penjualanCorporateIds ?? collect())
                ->unique();

            // Gabungkan perorangan ID dari SEMUA layanan
            $allPeroranganIds = ($projectPeroranganIds ?? collect())
                ->merge($sewaPeroranganIds ?? collect())
                ->merge($kalibrasiPeroranganIds ?? collect())
                ->merge($penjualanPeroranganIds ?? collect())
                ->unique();

            $customerBaru = $allCorporateIds->count() + $allPeroranganIds->count();
        } elseif ($serviceType === 'Layanan Pemetaan') {
            $customerBaru = ($projectCorporateIds ?? collect())->unique()->count() + ($projectPeroranganIds ?? collect())->unique()->count();
        } elseif ($serviceType === 'Layanan Sewa') {
            $customerBaru = ($sewaCorporateIds ?? collect())->unique()->count() + ($sewaPeroranganIds ?? collect())->unique()->count();
        } elseif ($serviceType === 'Layanan Servis dan Kalibrasi') {
            $customerBaru = ($kalibrasiCorporateIds ?? collect())->unique()->count() + ($kalibrasiPeroranganIds ?? collect())->unique()->count();
        } elseif ($serviceType === 'Layanan Penjualan Alat') {
            $customerBaru = ($penjualanCorporateIds ?? collect())->unique()->count() + ($penjualanPeroranganIds ?? collect())->unique()->count();
        }

        return [
            Stat::make('Pendapatan Bersih', 'Rp' . $formatNumber($pendapatanBersih))
                ->description('Pendapatan bersih dalam rentang filter')
                ->color('secondary'),
            Stat::make('Total Customer', $formatNumber($customerBaru))
                ->description('Customer baru dalam rentang filter')
                ->color('primary'),
            Stat::make('Total Pesanan', $formatNumber($pesananBaru))
                ->description('Total pesanan dalam rentang filter')
                ->color('success'),
        ];
    }
}
