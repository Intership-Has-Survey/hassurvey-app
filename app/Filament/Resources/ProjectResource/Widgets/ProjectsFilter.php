<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

class ProjectsFilter extends BaseDashboard
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
                        DateRangePicker::make('created_at')
                            ->label('Filter Berdasarkan Rentang Tanggal'),
                    ])
                    ->columns(3),
            ]);
    }
}
