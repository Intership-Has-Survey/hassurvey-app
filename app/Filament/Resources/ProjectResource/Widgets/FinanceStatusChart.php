<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use App\Models\StatusPembayaran;
use App\Models\TransaksiPembayaran;
use App\Models\PengajuanDana;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinanceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemasukan & Pengeluaran';
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
        // Ambil semua project ID (atau bisa difilter sesuai kebutuhan)
        $projectIds = Project::pluck('id');

        // --- Query Pemasukan (StatusPembayaran) ---
        $pendapatan = StatusPembayaran::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month_label"),
                DB::raw("SUM(nilai) as total")
            )
            ->whereIn('payable_id', $projectIds)
            ->where('payable_type', Project::class)
            ->when($this->startDate, fn($q) => $q->where('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->where('created_at', '<=', $this->endDate))
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        // --- Query Pengeluaran (TransaksiPembayaran + PengajuanDana -> Project) ---
        $pengeluaran = TransaksiPembayaran::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month_label"),
                DB::raw("SUM(nilai) as total")
            )
            ->whereHasMorph('payable', [PengajuanDana::class], function (Builder $q) use ($projectIds) {
                $q->whereHas('project', function (Builder $q2) use ($projectIds) {
                    $q2->whereIn('id', $projectIds);
                });
            })
            ->when($this->startDate, fn($q) => $q->where('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->where('created_at', '<=', $this->endDate))
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        // --- Gabungkan semua bulan dari kedua query ---
        $labels = collect()
            ->merge($pendapatan->pluck('month_label', 'month_key'))
            ->merge($pengeluaran->pluck('month_label', 'month_key'))
            ->unique()
            ->sortKeys()
            ->values();

        // --- Dataset Chart ---
        $datasets = [
            [
                'label' => 'Pemasukan',
                'data' => $labels->map(function ($label) use ($pendapatan) {
                    $item = $pendapatan->firstWhere('month_label', $label);
                    return $item ? (float) $item->total : 0;
                })->toArray(),
                'borderColor' => '#10B981',
                'backgroundColor' => '#10B981',
                'fill' => false,
                'tension' => 0.3,
            ],
            [
                'label' => 'Pengeluaran',
                'data' => $labels->map(function ($label) use ($pengeluaran) {
                    $item = $pengeluaran->firstWhere('month_label', $label);
                    return $item ? (float) $item->total : 0;
                })->toArray(),
                'borderColor' => '#EF4444',
                'backgroundColor' => '#EF4444',
                'fill' => false,
                'tension' => 0.3,
            ],
        ];

        return [
            'datasets' => $datasets,
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // bisa diganti 'bar'
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
