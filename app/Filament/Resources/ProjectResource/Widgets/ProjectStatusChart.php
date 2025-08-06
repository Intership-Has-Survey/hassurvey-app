<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProjectStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Status Proyek';
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter Tanggal')
                ->icon('heroicon-o-funnel')
                ->form([
                    DatePicker::make('startDate')
                        ->label('Tanggal Mulai')
                        ->default($this->startDate),
                    DatePicker::make('endDate')
                        ->label('Tanggal Selesai')
                        ->default($this->endDate),
                ])
                ->action(function (array $data) {
                    $this->startDate = $data['startDate'];
                    $this->endDate = $data['endDate'];
                }),
        ];
    }

    protected function getData(): array
    {
        $query = Project::query()
            ->select(
                'status',
                DB::raw("DATE_FORMAT(tanggal_informasi_masuk, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->groupBy('status', 'month')
            ->orderBy('month');

        if ($this->startDate) {
            $query->where('tanggal_informasi_masuk', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('tanggal_informasi_masuk', '<=', $this->endDate);
        }

        $data = $query->get();

        $statuses = ['Prospect', 'Follow up 1', 'Follow up 2', 'Follow up 3', 'Closing', 'Failed'];
        $datasets = [];
        $labels = $data->pluck('month')->unique()->sort()->values();

        foreach ($statuses as $status) {
            $datasets[] = [
                'label' => $status,
                'data' => $labels->map(function ($month) use ($data, $status) {
                    return $data->where('month', $month)->where('status', $status)->first()->count ?? 0;
                })->toArray(),
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
