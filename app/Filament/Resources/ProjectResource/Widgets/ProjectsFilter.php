<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Forms\Contracts\HasForms;

class ProjectsFilter extends Widget implements HasForms
{
    use InteractsWithForms;
    use InteractsWithPageFilters;

    protected static string $view = 'filament.resources.project-resource.widgets.projects-filter';

    public ?array $filters = null;

    public function mount(): void
    {
        $this->form->fill($this->filters);
    }

    public function updateFilters(array $data): void
    {
        // Merge filter baru dengan filter lama
        $this->filters = array_merge($this->filters ?? [], $data);

        // Simpan perubahan ke page filter supaya widget lain (misalnya statistik) ikut ke-refresh
        $this->dispatch('updatePageFilters', filters: $this->filters);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make('created_at')
                    ->label('Filter Berdasarkan Rentang Tanggal')
                    ->reactive()
                    ->afterStateUpdated(fn($state) => $this->updateFilters(['created_at' => $state])),
            ])->columns(1)
            ->statePath('filters'); // ini penting, supaya nyambung ke $filters page
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View Dashboard');
    }
}
