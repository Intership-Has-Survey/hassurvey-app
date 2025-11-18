<?php

namespace App\Filament\Resources;

use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BankAccount;
use Filament\Pages\Actions;
use App\Models\PengajuanDana;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\PengajuanDanaResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Filament\Resources\PengajuanDanaResource\Pages\EditPengajuanDana;
use App\Filament\Resources\PengajuanDanaResource\Pages\ViewPengajuanDana;
use App\Filament\Resources\PengajuanDanaResource\Pages\ListPengajuanDanas;
use App\Filament\Resources\PengajuanDanaResource\Pages\CreatePengajuanDana;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers\DetailPengajuansRelationManager;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers\TransaksiPembayaransRelationManager;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers\ConcreteTransaksiPembayaransRelationManager;

class PengajuanDanaResource extends Resource
{
    use HasFiltersForm;

    protected static ?string $model = PengajuanDana::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pengajuan Dana';

    protected static ?string $tenantRelationshipName = 'pengajuanDanas';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Pengajuan Dana';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pengajuan')
                    ->schema([
                        TextInput::make('judul_pengajuan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('deskripsi_pengajuan')
                            ->label('Deskripsi Umum')
                            ->columnSpanFull(),

                        Select::make('bank_id')
                            ->relationship('bank', 'nama_bank')
                            ->placeholder('Pilih Bank')
                            ->searchable()
                            ->preload()
                            ->label('Daftar Bank')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama bank wajib diisi.',
                            ])
                            ->reactive()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(fn(callable $set) => $set('bank_account_id', null)),

                        Select::make('bank_account_id')
                            ->label('Nomor Rekening')
                            ->options(function (callable $get) {
                                $bankId = $get('bank_id');
                                if (!$bankId) {
                                    return [];
                                }
                                return BankAccount::where('bank_id', $bankId)
                                    ->get()
                                    ->mapWithKeys(function ($account) {
                                        return [$account->id => "{$account->no_rek} ({$account->nama_pemilik})"];
                                    });
                            })
                            ->reactive()
                            ->validationMessages([
                                'required' => 'Nomor Rekening wajib diisi.',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih Nomor Rekening')
                            ->createOptionForm([
                                TextInput::make('no_rek')
                                    ->label('Nomor Rekening')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('nama_pemilik')
                                    ->label('Nama Pemilik')
                                    ->required(),
                                Hidden::make('user_id')
                                    ->default(auth()->id()),
                            ])
                            ->createOptionUsing(function (array $data, callable $get): string {
                                $data['bank_id'] = $get('bank_id');
                                $account = BankAccount::create($data);
                                return $account->id;
                            })
                            ->required(),
                    ])->columns(2),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul_pengajuan')
                    // ->searchable()
                    ->description(function (PengajuanDana $record): string {
                        if ($record->pengajuanable) {
                            switch (class_basename($record->pengajuanable_type)) {
                                case 'Project':
                                    return 'Untuk Proyek: ' . $record->pengajuanable->nama_project;
                                case 'Sewa':
                                    return 'Untuk Sewa: ' . $record->pengajuanable->judul;
                                case 'Penjualan':
                                    return 'Untuk Penjualan: ' . $record->pengajuanable->nama;
                                case 'Kalibrasi':
                                    return 'Untuk Kalibrasi: ' . $record->pengajuanable->nama;
                            }
                        }
                        return 'Untuk: In-House (Internal)';
                    })
                    ->searchable(),
                TextColumn::make('nilai')
                    ->sortable()
                    ->money('IDR'),
                // TextColumn::make('total')
                //     ->state(function (PengajuanDana $record): float {
                //         return $record->detailPengajuans->reduce(function ($carry, $item) {
                //             return $carry + ($item->qty * $item->harga_satuan);
                //         }, 0);
                //     })
                //     ->money('IDR'),
                TextColumn::make('level.nama')
                    ->label('Level')
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->label('Dalam Review')
                    ->getStateUsing(function ($record) {
                        if ($record->dalam_review === 'approved') {
                            return 'approved';
                        }
                        // Jika dalam_review adalah angka (role_id), ambil nama role
                        $role = Role::find($record->dalam_review);
                        return $role ? $role->name : '-';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Baru', 'Menunggu Persetujuan DO', 'Menunggu Persetujuan DK', 'Menunggu Persetujuan DU' => 'warning',
                        'operasional' => 'gray',
                        'dirops' => 'primary',
                        'keuangan' => 'warning',
                        'direktur' => 'info',
                        'approved' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('disetujui')
                    ->label('Disetujui')
                    ->toggleable()
                    ->default('-'),
                TextColumn::make('ditolak')
                    ->label('Ditolak')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        // Hitung ulang status
                        $totalTagihan = $record->detailPengajuans()->sum('total');
                        $totalPembayaran = $record->statusPengeluarans()->sum('nilai');

                        $statusBaru = null;

                        if ($totalTagihan == 0 && $totalPembayaran == 0) {
                            $statusBaru = 3; // Belum Ada Tagihan
                        } elseif ($totalTagihan == $totalPembayaran) {
                            $statusBaru = 1; // Lunas
                        } elseif ($totalTagihan > $totalPembayaran) {
                            $statusBaru = 0; // Belum Bayar
                        } else {
                            $statusBaru = 2; // Lebih Bayar
                        }

                        // Update hanya jika berbeda
                        if ($record->status !== $statusBaru) {
                            $record->status = $statusBaru;
                            $record->saveQuietly(); // supaya tidak trigger event berkali-kali
                        }

                        // Return label berdasarkan status
                        return match ($record->status) {
                            0 => 'Belum Bayar',
                            1 => 'Lunas',
                            2 => 'Lebih Bayar',
                            3 => 'Belum Ada Tagihan',
                            default => '-',
                        };
                    })
                    ->badge()
                    ->colors([
                        'info' => fn($state) => $state === 'Belum Ada Tagihan',
                        'success' => fn($state) => $state === 'Lunas',
                        'danger' => fn($state) => $state === 'Belum Bayar',
                        'warning' => fn($state) => $state === 'Lebih Bayar',
                    ])
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Belum Ada Pengajuan Dana Terdaftar')
            ->emptyStateDescription('Silahkan buat pengajuan dana untuk memulai.')
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('pengajuanable_type')
                    ->options([
                        'App\Models\Project' => 'Project',
                        'App\Models\Sewa' => 'Sewa',
                        'App\Models\Kalibrasi' => 'Kalibrasi',
                        'App\Models\Penjualan' => 'Penjualan',
                        'App\Models\PengajuanDana' => 'In-House',
                    ])
                    ->label('Jenis Pengajuan')
                    ->multiple(),
                SelectFilter::make('status')
                    ->options([
                        '0' => 'Belum Bayar',
                        '1' => 'Lunas',
                        '2' => 'Lebih Bayar',
                        '3' => 'Belum Ada Tagihan',
                    ])
                    ->label('Status'),

                SelectFilter::make('dalam_review')
                    ->options([
                        '0' => '-',
                        '4' => 'Direktur Keuangan',
                        '6' => 'Direktur Utama',
                    ])
                    ->label('Dalam Review'),

                Filter::make('created_at')
                    ->form([
                        Grid::make()->schema([
                            DatePicker::make('created_from'),
                            DatePicker::make('created_until')
                        ])
                    ])
                    ->columnSpan(2)

                    //parameter pertama query, kedua data dari objeck ini
                    //mengembalikan query menggunakan arrow function
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->deferFilters()
            ->actions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->dalam_review == auth()->user()->roles->first()?->id)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->approve()),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')

                    ->visible(fn($record) => $record->dalam_review == auth()->user()->roles->first()?->id)
                    ->form([
                        Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($record, array $data) {
                        $record->reject($data['alasan']);
                    }),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $pdf = Pdf::loadView('exports.pengajuan', ['record' => $record]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'sales-' . $record->id . '.pdf');
                    }),

                ActivityLogTimelineTableAction::make('Log'),
                // DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->except([
                                'user.name',
                            ])
                            ->withFilename('Export-Pengajuan-' . date('Y-m-d'))
                            ->withColumns([
                                Column::make('pengajuanable_type')
                                    ->heading('Jenis Pengajuan')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'App\Models\Project' => 'Project',
                                        'App\Models\Sewa' => 'Sewa',
                                        'App\Models\Penjualan' => 'Penjualan',
                                        'App\Models\Kalibrasi' => 'Kalibrasi',
                                        'App\Models\PengajuanDana' => 'In-House (Internal)',
                                        default => $state,
                                    }),
                                Column::make('judul_pengajuan'),
                                Column::make('deskripsi_pengajuan'),
                            ]),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DetailPengajuansRelationManager::class,
            \App\Filament\Resources\PengajuanDanaResource\RelationManagers\ConcreteTransaksiPembayaransRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanDanas::route('/'),
            'create' => Pages\CreatePengajuanDana::route('/create'),
            'edit' => Pages\EditPengajuanDana::route('/{record}/edit'),
            'view' => Pages\ViewPengajuanDana::route('/{record}'),
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }

    public static function afterCreate($record): void
    {
        if (!$record->pengajuanable_id) {
            $record->update([
                'pengajuanable_id' => $record->id,
                'pengajuanable_type' => \App\Models\PengajuanDana::class,
            ]);
        }
    }

    public static function getWidgets(): array
    {
        return [
            PengajuanDanaResource\Widgets\PengajuanDanaOverview::class,
        ];
    }
}
