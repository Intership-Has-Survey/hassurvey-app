<?php

namespace App\Filament\Traits;

use Filament\Actions\Action;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Carbon\Carbon;

trait HasDateFilter
{
    public ?string $dateRange = null;

    protected function getHeaderActions(): array
    {
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
                    $this->dispatch('refreshWidget');
                }),
        ];
    }

    protected function getDateRange(): array
    {
        if ($this->dateRange) {
            [$startDate, $endDate] = explode(' - ', $this->dateRange);
            return [
                'start' => $startDate,
                'end' => $endDate,
            ];
        }

        return [
            'start' => now()->subDays(30)->format('Y-m-d'),
            'end' => now()->format('Y-m-d'),
        ];
    }

    protected function applyDateFilter($query, $dateColumn = 'created_at')
    {
        $range = $this->getDateRange();

        return $query->whereBetween($dateColumn, [
            $range['start'] . ' 00:00:00',
            $range['end'] . ' 23:59:59'
        ]);
    }
}
