<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;

class ProjectTypeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Online', Project::query()->where('sumber', 'online')->count()),
            Stat::make('Offline', Project::query()->where('sumber', 'offline')->count()),
            // Stat::make('Rabbits', Project::query()->where('kategori', 'rabbit')->count()),
        ];
    }
}
