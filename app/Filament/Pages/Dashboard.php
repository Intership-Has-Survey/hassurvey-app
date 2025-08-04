<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Sewa;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\GrafikCustomer;
use App\Filament\Widgets\GrafikCustomerBulan;
use App\Filament\Widgets\GrafikPesanan;
use App\Filament\Widgets\GrafikPesananBulan;
use App\Filament\Widgets\StatsOverview;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static bool $isLazy = false;

    public function filtersForm(Form $form): Form
    {
        // $earliestProjectDate = Project::min('tanggal_informasi_masuk');
        // $earliestSewaDate = Sewa::min('tgl_mulai');
        // $minDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');

        return $form
            ->schema([
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
            ]);
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
