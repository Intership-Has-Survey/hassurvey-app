<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GrafikCustomer;
use App\Filament\Widgets\GrafikCustomerBulan;
use App\Filament\Widgets\GrafikPesanan;
use App\Filament\Widgets\GrafikPesananBulan;
use App\Filament\Widgets\StatsOverview;
use App\Models\Project;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Forms\Components\Placeholder;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static bool $isLazy = false;

    public function filtersForm(Form $form): Form
    {
        // $earliestProjectDate = Project::min('tanggal_informasi_masuk');
        // $earliestSewaDate = Sewa::min('tgl_mulai');
        // $minDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');

        $user = auth()->user();
        $nama = $user->name ?? 'Pengguna';


        // Cek apakah user boleh akses form filter
        $canUseFilter = $user->hasRole('Super Admin') || $user->can('View Dashboard');

        if (! $canUseFilter) {
            // Jika user tidak punya izin filter, tampilkan pesan sambutan
            return $form
                ->schema([
                    Section::make('👋 Halo, Selamat Datang di Dashboard')
                        ->schema([
                            Placeholder::make('Selamat Datang')
                                ->content("Halo kak {$nama}, Kami senang Anda bergabung hari ini. Silakan jelajahi modul yang tersedia untuk melanjutkan aktivitas Anda. 
                Jika beberapa fitur belum dapat diakses, mohon hubungi Super Admin untuk mendapatkan izin. Terima kasih atas kerja samanya 🙏"),
                        ]),
                ]);
        }

        return $form
            ->schema($canUseFilter ? [
                Section::make()
                    ->schema([
                        Select::make('serviceType')
                            ->options([
                                'Semua' => 'Semua',
                                'Layanan Pemetaan' => 'Layanan Pemetaan',
                                'Layanan Sewa' => 'Layanan Sewa',
                                'Layanan Servis dan Kalibrasi' => 'Layanan Servis dan Kalibrasi',
                                'Layanan Penjualan Alat' => 'Layanan Penjualan Alat',
                            ])
                            ->default('Semua')
                            ->in(['Semua', 'Layanan Pemetaan', 'Layanan Sewa', 'Layanan Servis dan Kalibrasi', 'Layanan Penjualan Alat']),
                        DateRangePicker::make('created_at')
                            ->label('Filter Berdasarkan Rentang Tanggal'),
                    ])
                    ->columns(3),
            ] : []);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            GrafikCustomer::class,
            GrafikCustomerBulan::class,
            GrafikPesanan::class,
            GrafikPesananBulan::class,
        ];
    }
}
