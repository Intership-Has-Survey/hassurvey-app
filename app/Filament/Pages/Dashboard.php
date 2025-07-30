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

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static bool $isLazy = false;

    public function filtersForm(Form $form): Form
    {
        $earliestProjectDate = Project::min('tanggal_informasi_masuk');
        $earliestSewaDate = Sewa::min('tgl_mulai');
        $minDate = collect([$earliestProjectDate, $earliestSewaDate])->filter()->min() ?? Carbon::parse('2000-01-01');

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('serviceType')
                            ->label('Jenis Layanan')
                            ->options([
                                'Semua' => 'Semua',
                                'Layanan Pemetaan' => 'Layanan Pemetaan',
                                'Layanan Sewa' => 'Layanan Sewa',
                                //'Layanan Servis dan Kalibrasi' => 'Layanan Servis dan Kalibrasi',
                                //'Layanan Penjualan Alat' => 'Layanan Penjualan Alat', 
                            ]),
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->minDate($minDate)
                            ->maxDate(fn(Get $get) => $get('endDate') ?: now())
                            ->default($minDate),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->minDate(fn(Get $get) => $get('startDate') ?: $minDate)
                            ->maxDate(now())
                            ->default(now()),
                    ])
                    ->columns(3),
            ]);


    }

    protected static array $widgets = [
        StatsOverview::class,
        GrafikCustomer::class,
        GrafikCustomerBulan::class,
        GrafikPesanan::class,
        GrafikPesananBulan::class,
    ];

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
