<?php

namespace App\Filament\Resources\StatusPembayaranResource\Pages;

use Filament\Actions;
use App\Models\Project;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\RingkasanPembayaran;
use App\Filament\Resources\StatusPembayaranResource;

class ListStatusPembayarans extends ListRecords
{
    protected static string $resource = StatusPembayaranResource::class;

    public ?Project $project = null;

    /**
     * Modifikasi query utama untuk memfilter berdasarkan project_id dari URL.
     */

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($projectId = request()->get('project_id')) {
            $this->project = Project::find($projectId);
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    /**
     * Membuat judul halaman menjadi dinamis.
     */
    public function getTitle(): string
    {
        if ($this->project) {
            return 'Riwayat Pembayaran Projek ' . $this->project->nama_project;
        }
        return parent::getTitle();
    }

    /**
     * Membuat breadcrumbs menjadi dinamis.
     */
    public function getBreadcrumbs(): array
    {
        if ($this->project) {
            return [
                RingkasanPembayaran::getUrl() => 'Ringkasan Pembayaran',
                '#' => $this->getTitle(),
            ];
        }
        return parent::getBreadcrumbs();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
