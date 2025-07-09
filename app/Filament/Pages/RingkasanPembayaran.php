<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Concerns\InteractsWithTable;

class RingkasanPembayaran extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Ringkasan Pembayaran';
    protected static ?string $title = 'Ringkasan Pembayaran per Proyek';
    protected static string $view = 'filament.pages.ringkasan-pembayaran';
    protected static ?int $navigationSort = 4; // Atur urutan di menu

    /**
     * Mendefinisikan struktur tabel untuk halaman ini.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->whereHas('StatusPembayaran'))
            ->columns([
                TextColumn::make('nama_project')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nilai_project')
                    ->label('Nilai Project')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_dibayar')
                    ->label('Total Dibayar')
                    ->money('IDR')
                    ->state(function (Project $record): float {
                        // Menghitung total pembayaran dari relasi
                        return $record->StatusPembayaran()->sum('nilai');
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withSum('statuspembayaran as total_dibayar', 'nilai')
                            ->orderBy('total_dibayar', $direction);
                    }),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state === 'Lunas' => 'success',
                        str_contains($state, '%') => 'warning',
                        default => 'danger',
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Project $record): string => ProjectResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
