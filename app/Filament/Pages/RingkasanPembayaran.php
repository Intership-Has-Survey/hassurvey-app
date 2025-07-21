<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Filament\Resources\StatusPembayaranResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\StatusPembayaran;

class RingkasanPembayaran extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Pemasukan';
    protected static ?string $title = 'Daftar Pembayaran';
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
                // ->selectRaw('id, payable_id, SUM(nilai) as total_dibayar')
                // ->groupBy('id', 'payable_id')
            )
            ->columns([
                // TextColumn::make('nama_pembayaran')->label('Nama Pembayaran'),
                // TextColumn::make('jenis_pembayaran'),
                TextColumn::make('total_dibayar'),
                TextColumn::make('payable_id'),
                TextColumn::make('payable_type')
                    ->label('Jenis Layanan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\\Models\\Project' => 'Project',
                        'App\\Models\\Sewa' => 'Sewa',
                        'App\\Models\\Servis' => 'Servis',
                        default => 'Lainnya'
                    }),
                TextColumn::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->getStateUsing(fn(StatusPembayaran $record) => $record->payable?->nama_project ?? $record->payable?->nama_sewa ?? '-'),
            ])
            ->actions([
                // Action::make('view_payments')
                //     ->label('Lihat Riwayat Pembayaran')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn(Project $record): string => StatusPembayaranResource::getUrl('index', [
                //         'project_id' => $record->id,
                //     ])),

                // Action::make('view_payments')
                //     ->label('LIHAT')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn(StatusPembayaran $record): string => StatusPembayaranResource::getUrl('index', [
                //         'payable_id' => $record->id,
                //     ]))
                //     ->openUrlInNewTab(),
            ]);
    }
}
