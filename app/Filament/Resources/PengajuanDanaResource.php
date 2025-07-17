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
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaResource extends Resource
{
    protected static ?string $model = PengajuanDana::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pengajuan Dana';

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
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pembayaran (Rekening Tujuan)')
                    ->schema([
                        Forms\Components\TextInput::make('nama_bank')->maxLength(255),
                        Forms\Components\TextInput::make('nomor_rekening')->maxLength(255),
                        Forms\Components\TextInput::make('nama_pemilik_rekening')->maxLength(255),
                    ])->columns(3),

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

                Tables\Columns\TextColumn::make('dalam_review')
                    ->badge()
                    ->label('Dalam Review')
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
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->color('primary')
                    ->button()
                    ->requiresConfirmation()
                    ->visible(fn() => auth()->user()->role !== 'operasional')
                    ->disabled(function (Model $record) {
                        return auth()->user()->role !== $record->dalam_review;
                    })
                    // ->visible(fn(Model $record) => $record->status !== 'gold') // hanya tampil jika belum gold
                    ->action(function (Model $record) {
                        $review = ['dirops', 'keuangan', 'direktur', 'approved'];
                        $currentIndex = array_search($record->dalam_review, $review);

                        if ($currentIndex !== false && $currentIndex < count($review) - 1) {
                            $record->update([
                                'dalam_review' => $review[$currentIndex + 1],
                                'disetujui' => auth()->user()->role,
                                'ditolak' => null,
                                'alasan' => null,
                            ]);
                        }
                    }),
                Action::make('Tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->role !== 'operasional')
                    ->form([
                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->disabled(function (Model $record) {
                        return auth()->user()->role !== $record->dalam_review;
                    })
                    ->action(function (Model $record, array $data) {
                        $review = ['dirops', 'keuangan', 'direktur', 'approved'];
                        $currentIndex = array_search($record->dalam_review, $review);

                        if ($currentIndex !== false) {
                            // Turunkan level jika bisa (misal dari gold → silver)
                            $newStatus = $record->dalam_review;
                            if ($currentIndex > 0) {
                                $newStatus = $review[$currentIndex - 1];
                            }

                            // Simpan status baru + alasan
                            $record->update([
                                'dalam_review' => $newStatus,
                                'ditolak' => auth()->user()->role,
                                'disetujui' => null,
                                'alasan' => $data['alasan'], // pastikan kolom ini ada di tabel
                            ]);
                        }
                    }),
                // ->disabled(function (Model $record) {
                //     $role = auth()->user()->role;
                //     return !(
                //         ($role === 'keuangan' && $record->status === 'bronze') ||
                //         ($role === 'direktur' && $record->status === 'silver')
                //     );
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

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['operasional', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        $userRole = auth()->user()?->role;

        return match ($userRole) {
            'admin'     => true,
            'operasional'  => $record->dalam_review === 'dirops',
            'dirops'  => $record->dalam_review === 'dirops',
            'keuangan'  => $record->dalam_review === 'keuangan',
            'direktur'  => $record->dalam_review === 'direktur',

            // 'keuangan'  => $record->dalam_review === 'bronze',
            // 'direktur'  => $record->dalam_review === 'silver',
            default     => false, // selain itu tidak boleh edit
        };
    }
}
