<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('businessCustomersOnly')
                            ->label('Jenis Layanan')
                            ->options([
                                'Semua' => 'Semua',
                                'Layanan Pemetaan' => 'Layanan Pemetaan',
                                'Layanan Sewa' => 'Layanan Sewa',
                                'Layanan Servis dan Kalibrasi' => 'Layanan Servis dan Kalibrasi',
                                'Layanan Penjualan Alat' => 'Layanan Penjualan Alat',
                            ]),
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->maxDate(fn(Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->minDate(fn(Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
