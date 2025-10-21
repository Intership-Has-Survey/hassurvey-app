<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProjectResource;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Resources\ProjectResource\Widgets\ProjectsFilter;
use App\Filament\Resources\ProjectResource\Widgets\FinanceStatusChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatusChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

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
            ExportAction::make()
                ->label('Export ke Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->exports([
                    ExcelExport::make('table')
                        ->label('Export Kolom Tabel (Sederhana)')
                        ->fromTable()
                        ->askForWriterType()
                        ->askForFilename()
                        ->withFilename(fn () => 'export-proyek-pemetaan-table-' . date('Y-m-d-His')),
                    
                    ExcelExport::make('lengkap')
                        ->label('Export Lengkap (Semua Kolom)')
                        ->askForWriterType()
                        ->askForFilename()
                        ->withFilename(fn () => 'export-proyek-pemetaan-lengkap-' . date('Y-m-d-His'))
                        ->withColumns([
                            Column::make('kode_project')->heading('Kode Proyek'),
                            Column::make('nama_project')->heading('Nama Proyek'),
                            Column::make('status')->heading('Status Proyek'),
                            Column::make('customer_display')
                                ->heading('Klien Utama')
                                ->formatStateUsing(function ($record) {
                                    if ($record->corporate) {
                                        return $record->corporate->nama;
                                    }
                                    return $record->perorangan->first()?->nama ?? 'N/A';
                                }),
                            Column::make('perorangan')
                                ->heading('PIC (Person in Charge)')
                                ->formatStateUsing(function ($state) {
                                    return $state->pluck('nama')->implode(', ');
                                }),
                            Column::make('kategori.nama')->heading('Kategori'),
                            Column::make('sales.nama')->heading('Sales'),
                            Column::make('tanggal_informasi_masuk')->heading('Tanggal Info Masuk'),
                            Column::make('sumber')->heading('Sumber'),
                            Column::make('provinsiRegion.name')->heading('Provinsi'),
                            Column::make('kotaRegion.name')->heading('Kota/Kabupaten'),
                            Column::make('kecamatanRegion.name')->heading('Kecamatan'),
                            Column::make('desaRegion.name')->heading('Desa/Kelurahan'),
                            Column::make('detail_alamat')->heading('Detail Alamat'),
                            Column::make('nilai_project_awal')
                                ->heading('Nilai Proyek (Rp)')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                            Column::make('dikenakan_ppn')
                                ->heading('Dikenakan PPN')
                                ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                            Column::make('nilai_ppn')
                                ->heading('Nilai PPN (Rp)')
                                ->formatStateUsing(function ($record) {
                                    if ($record->dikenakan_ppn) {
                                        return number_format($record->nilai_project_awal * 0.12, 0, ',', '.');
                                    }
                                    return '0';
                                }),
                            Column::make('nilai_project')
                                ->heading('Total Tagihan (Rp)')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                            Column::make('status_pembayaran')->heading('Status Pembayaran'),
                            Column::make('status_pekerjaan')->heading('Status Pekerjaan'),
                            Column::make('created_at')
                                ->heading('Dibuat Pada')
                                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i:s') ?? '-'),
                            Column::make('updated_at')
                                ->heading('Diupdate Pada')
                                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i:s') ?? '-'),
                        ]),
                    
                    ExcelExport::make('ringkas')
                        ->label('Export Ringkas (Kolom Penting)')
                        ->askForWriterType()
                        ->askForFilename()
                        ->withFilename(fn () => 'export-proyek-pemetaan-ringkas-' . date('Y-m-d-His'))
                        ->withColumns([
                            Column::make('kode_project')->heading('Kode Proyek'),
                            Column::make('nama_project')->heading('Nama Proyek'),
                            Column::make('status')->heading('Status'),
                            Column::make('customer_display')
                                ->heading('Klien')
                                ->formatStateUsing(function ($record) {
                                    if ($record->corporate) {
                                        return $record->corporate->nama;
                                    }
                                    return $record->perorangan->first()?->nama ?? 'N/A';
                                }),
                            Column::make('sales.nama')->heading('Sales'),
                            Column::make('nilai_project')
                                ->heading('Total Tagihan (Rp)')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                            Column::make('status_pembayaran')->heading('Pembayaran'),
                            Column::make('status_pekerjaan')->heading('Pekerjaan'),
                            Column::make('tanggal_informasi_masuk')->heading('Tanggal'),
                        ]),
                ]),
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
            FinanceStatusChart::make(),
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
