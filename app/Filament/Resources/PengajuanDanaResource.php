<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\InHouse;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PengajuanDana;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\PengajuanDanaResource\Pages;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Level;

class PengajuanDanaResource extends Resource
{
    protected static ?string $model = PengajuanDana::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pengajuan Dana';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Pengajuan Dana';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengajuan')
                    ->schema([
                        Forms\Components\TextInput::make('judul_pengajuan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Hidden::make('tipe_pengajuan')
                            ->default('inhouse'),

                        Forms\Components\Textarea::make('deskripsi_pengajuan')
                            ->label('Deskripsi Umum')
                            ->columnSpanFull(),
                        Select::make('bank_id')
                            ->relationship('bank', 'nama_bank')
                            ->placeholder('Pilih Bank')
                            ->searchable()
                            ->preload()
                            ->label('Daftar Bank')
                            ->required()
                            ->reactive()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(fn(callable $set) => $set('bank_account_id', null)),

                        Forms\Components\Select::make('bank_account_id')
                            ->label('Nomor Rekening')
                            ->options(function (callable $get) {
                                $bankId = $get('bank_id');
                                if (!$bankId) {
                                    return [];
                                }

                                return \App\Models\BankAccount::where('bank_id', $bankId)
                                    ->get()
                                    ->mapWithKeys(function ($account) {
                                        return [$account->id => "{$account->no_rek} ({$account->nama_pemilik})"];
                                    });
                            })
                            ->reactive()
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih Nomor Rekening')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('no_rek')
                                    ->label('Nomor Rekening')
                                    ->required(),
                                Forms\Components\TextInput::make('nama_pemilik')
                                    ->label('Nama Pemilik')
                                    ->required(),
                                // Forms\Components\Hidden::make('bank_id')
                                //     ->default(fn(callable $get) => $get('bank_id')),
                                Forms\Components\Hidden::make('user_id')
                                    ->default(auth()->id()),
                            ])
                            ->createOptionUsing(function (array $data, callable $get): string {
                                // Ambil bank_id dari form utama
                                $data['bank_id'] = $get('bank_id');

                                $account = \App\Models\BankAccount::create($data);
                                return $account->id;
                            })
                            ->required(),
                    ])->columns(2),



                // Repeater::make('detailPengajuans') // nama relasi
                //     ->relationship()
                //     ->columnSpanFull()
                //     ->label('Rincian Pengajuan Dana')
                //     ->schema([
                //         TextInput::make('deskripsi')
                //             ->label('Nama Item')
                //             ->required(),
                //         TextInput::make('qty')
                //             ->label('Jumlah')
                //             ->required(),

                //         TextInput::make('harga_satuan')
                //             ->label('Harga Satuan')
                //             ->numeric()
                //             ->required(),
                //     ])
                //     ->defaultItems(1)
                //     ->createItemButtonLabel('Tambah Rincian')
                //     ->columns(3),
                Forms\Components\Hidden::make('nilai')
                    ->default('0'),
                Forms\Components\Hidden::make('dalam_review')
                    ->default('0'),


                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengajuan')
                    ->searchable()
                    ->description(function (PengajuanDana $record): string {
                        if ($record->project) {
                            return 'Untuk Proyek: ' . $record->project->nama_project;
                        } elseif ($record->sewa) {
                            return 'Untuk Sewa: ' . $record->sewa->judul;
                        }
                        return 'Untuk: In-House (Internal)';
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->state(function (PengajuanDana $record): float {
                        return $record->detailPengajuans->reduce(function ($carry, $item) {
                            return $carry + ($item->qty * $item->harga_satuan);
                        }, 0);
                    })
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Dalam Review')
                    ->getStateUsing(function ($record) {
                        if ($record->dalam_review === 'approved') {
                            return 'approved';
                        }

                        // Jika dalam_review adalah angka (role_id), ambil nama role
                        $role = \Spatie\Permission\Models\Role::find($record->dalam_review);
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
                    }),

                Tables\Columns\TextColumn::make('disetujui')->label('Disetujui'),
                Tables\Columns\TextColumn::make('ditolak')->label('Ditolak'),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat oleh'),
                Tables\Columns\TextColumn::make('level.nama')->label('Level'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->emptyStateHeading('Belum Ada Pengajuan Dana Terdaftar')
            ->emptyStateDescription('Silahkan buat pengajuan dana untuk memulai.')
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire, $record) {
                        // Hitung total harga dulu
                        $record->updateTotalHarga();

                        // Refresh record agar data terbaru terbaca
                        $record->refresh();

                        $nilai = $record->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleId = optional($firstStep?->roles)->id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleId, // Pastikan ini role_id
                            ]);
                        }
                    }),

                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    // ->button()
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
                        \Filament\Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($record, array $data) {
                        $record->reject($data['alasan']);
                    }),
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pengajuan', ['record' => $record]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'sales-' . $record->id . '.pdf');
                    }),

                // Action::make('Tolak')
                // ->label('Tolak')
                // ->color('danger')
                // ->visible(fn() => auth()->user()->role !== 'operasional')
                // ->form([
                //     Forms\Components\Textarea::make('alasan')
                //         ->label('Alasan Penolakan')
                //         ->required(),
                // ])
                // ->requiresConfirmation()
                // ->disabled(function (Model $record) {
                //     return auth()->user()->role !== $record->dalam_review;
                // })
                // ->action(function (Model $record, array $data) {
                //     $review = ['dirops', 'keuangan', 'direktur', 'approved'];
                //     $currentIndex = array_search($record->dalam_review, $review);

                //     if ($currentIndex !== false) {
                //         // Turunkan level jika bisa (misal dari gold → silver)
                //         $newStatus = $record->dalam_review;
                //         if ($currentIndex > 0) {
                //             $newStatus = $review[$currentIndex - 1];
                //         }

                //         // Simpan status baru + alasan
                //         $record->update([
                //             'dalam_review' => $newStatus,
                //             'ditolak' => auth()->user()->role,
                //             'disetujui' => null,
                //             'alasan' => $data['alasan'], // pastikan kolom ini ada di tabel
                //         ]);
                //     }
                // }),
                ActivityLogTimelineTableAction::make('Log'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPengajuansRelationManager::class,
            RelationManagers\TransaksiPembayaransRelationManager::class,
            // \Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanDanas::route('/'),
            'create' => Pages\CreatePengajuanDana::route('/create'),
            // 'view' => Pages\ViewPengajuanDana::route('/{record}'),
            'edit' => Pages\EditPengajuanDana::route('/{record}/edit'),
        ];
    }
}
