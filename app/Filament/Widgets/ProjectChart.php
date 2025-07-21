<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;
use App\Models\Kategori;
use Filament\Forms\Components\DatePicker;

class ProjectChart extends ChartWidget
{
    protected static ?string $heading = 'Project per Kategori';

    public ?string $filter = 'today';
    protected function getData(): array
    {
        $startDate = $this->filterFormData['start_date'] ?? null;
        $endDate = $this->filterFormData['end_date'] ?? null;

        $query = Kategori::query()->withCount([
            'projects' => function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ]);

        $categories = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Project',
                    'data' => $categories->pluck('projects_count'),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ],
                ],
            ],
            'labels' => $categories->pluck('nama_kategori'),
        ];
    }



    protected function getType(): string
    {
        return 'pie'; // atau 'doughnut'
    }

    // protected function getFilters(): ?array
    // {
    //     return [
    //         'date_range' => [
    //             'form' => [
    //                 DatePicker::make('start_date')
    //                     ->label('Start Date')
    //                     ->reactive(),
    //                 DatePicker::make('end_date')
    //                     ->label('End Date')
    //                     ->reactive(),
    //             ],
    //         ],
    //     ];
    // }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('start_date')
                ->label('Start Date')
                ->reactive(),
            DatePicker::make('end_date')
                ->label('End Date')
                ->reactive(),
        ];
    }
}
