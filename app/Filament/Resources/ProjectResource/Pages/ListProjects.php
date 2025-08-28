<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\ProjectsFilter;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatusChart;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListProjects extends ListRecords
{
    use InteractsWithPageFilters;
    use ExposesTableToWidgets;

    protected static string $resource = ProjectResource::class;
    protected static ?string $title = 'Layanan Proyek Pemetaan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Proyek Pemetaan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectsFilter::make(),
            ProjectStatsOverview::make(['filters' => $this->filters]),
            ProjectStatusChart::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)),
            'Prospect' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Prospect')),
            'Follow up 1' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Follow up 1')),
            'Follow up 2' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Follow up 2')),
            'Follow up 3' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Follow up 3')),
            'Closing' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Closing')),
            'Failed' => Tab::make()
                ->modifyQueryUsing(fn($query) => $this->applyFiltersToQuery($query)->where('status', 'Failed')),
        ];
    }

    protected function applyFiltersToQuery($query)
    {
        $dateRange = $this->filters['created_at'] ?? null;

        if ($dateRange && isset($dateRange['start'], $dateRange['end'])) {
            $query->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end'],
            ]);
        }

        return $query;
    }
}
