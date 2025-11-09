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
                DB::raw("DATE_FORMAT(tanggal_informasi_masuk, '%Y-%m') as month_key"),
                DB::raw("DATE_FORMAT(tanggal_informasi_masuk, '%b %Y') as month_label"),
                DB::raw('COUNT(*) as total')
            )
            ->when(
                $this->startDate,
                fn($q) =>
                $q->where('tanggal_informasi_masuk', '>=', $this->startDate)
            )
            ->when(
                $this->endDate,
                fn($q) =>
                $q->where('tanggal_informasi_masuk', '<=', $this->endDate)
            )
            ->groupBy('status', 'month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        $labels = $query->pluck('month_label', 'month_key')->unique()->sortKeys()->values();

        $statuses = [
            'Prospect' => '#3B82F6',     // biru
            'Follow up 1' => '#F97316',  // oranye
            'Follow up 2' => '#FACC15',  // kuning
            'Follow up 3' => '#8B5CF6',  // ungu
            'Closing' => '#10B981',      // hijau
            'Failed' => '#EF4444',       // merah
        ];

        $datasets = [];

        foreach ($statuses as $status => $color) {
            $datasets[] = [
                'label' => $status,
                'data' => $labels->map(function ($label) use ($query, $status) {
                    $item = $query->firstWhere(
                        fn($row) =>
                        $row->month_label === $label && $row->status === $status
                    );
                    return $item ? (int) $item->total : 0;
                })->toArray(),
                'borderColor' => $color,
                'backgroundColor' => $color,
                'fill' => false,
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $data = $this->getData();

        $hasData = collect($data['datasets'])
            ->flatMap(fn($ds) => $ds['data'])
            ->sum() > 0;

        if (! $hasData) {
            return view('filament.widgets.empty-chart', [
                'heading' => static::$heading,
                'message' => 'Tidak ada data untuk periode ini.',
            ]);
        }

        return parent::render();
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
