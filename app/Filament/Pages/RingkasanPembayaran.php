<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Filament\Resources\StatusPembayaranResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\SewaResource;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\StatusPembayaran;

class RingkasanPembayaran extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Ringkasan Pembayaran';
    protected static ?string $title = 'Ringkasan Pembayaran';
    protected static string $view = 'filament.pages.ringkasan-pembayaran';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Keuangan';

    /**
     * Mendefinisikan struktur tabel untuk halaman ini.
     */
    public function table(Table $table): Table
    {
        return $table
            // ->query(StatusPembayaran::query()->with('payable'))
            // ->query(
            //     StatusPembayaran::query()
            //         ->whereIn('id', function ($query) {
            //             $query->selectRaw('MAX(id)')
            //                 ->from('status_pembayarans')
            //                 ->groupBy('payable_id');
            //         })
            //         ->with('payable')
            // )
            ->query(
                StatusPembayaran::query()
                    ->selectRaw('payable_id as id, SUM(nilai) as total_dibayar,payable_type')
                    ->groupBy('payable_id', 'payable_type')
            )
            ->columns([
                TextColumn::make('payable_type')
                    ->label('Jenis Layanan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\\Models\\Project' => 'Jasa Pemetaan',
                        'App\\Models\\Sewa' => 'Sewa',
                        'App\\Models\\Kalibrasi' => 'Kalibrasi',
                        default => 'Lainnya'
                    }),
                TextColumn::make('nama_layanan')
                    ->label('Judul Layanan')
                    ->getStateUsing(function ($record) {
                        if ($record->payable_type === 'App\\Models\\Project') {
                            return Project::find($record->id)?->nama_project ?? '-';
                        } elseif ($record->payable_type === 'App\\Models\\Sewa') {
                            return \App\Models\Sewa::find($record->id)?->judul ?? '-';
                        } elseif ($record->payable_type === 'App\\Models\\Kalibrasi') {
                            return \App\Models\Kalibrasi::find($record->id)?->nama ?? '-';
                        }
                        return '-';
                    }),
                TextColumn::make('Total Harga')
                    ->label('Harga Layanan')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        if ($record->payable_type === 'App\\Models\\Project') {
                            return Project::find($record->id)?->nilai_project ?? '-';
                        } elseif ($record->payable_type === 'App\\Models\\Sewa') {
                            return \App\Models\Sewa::find($record->id)?->harga_fix ?? '-';
                        }
                        return '-';
                    }),
                TextColumn::make('total_dibayar')
                    ->money('IDR'),
            ])
            ->actions([
                // Action::make('view_payments')
                //     ->label('Lihat Riwayat Pembayaran')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn(Project $record): string => StatusPembayaranResource::getUrl('index', [
                //         'project_id' => $record->id,
                //     ])),



                Action::make('view_payments')
                    ->label('LIHAT')
                    ->icon('heroicon-o-eye')
                    ->url(function (StatusPembayaran $record) {
                        if ($record->payable_type === 'App\\Models\\Project' && $record->id) {
                            return ProjectResource::getUrl('edit', [
                                'record' => $record->id,
                            ]) . '?activeRelationManager=1#status_pembayarans';
                        } elseif ($record->payable_type === 'App\\Models\\Sewa' && $record->id) {
                            return SewaResource::getUrl('edit', [
                                'record' => $record->id,
                            ]) . '?activeRelationManager=2#status_pembayarans';
                        }
                        return null; // Jangan kembalikan URL kosong
                    })
                // ->visible(
                //     fn(StatusPembayaran $record): bool =>
                //     $record->payable_type === 'App\\Models\\Project' && $record->payable_id !== null
                // )

                // ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Belum Ada Pemasukan Tercatat')
            ->defaultSort('created_at', 'desc');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola pembayaran'); // atau permission spesifik
    }
}
