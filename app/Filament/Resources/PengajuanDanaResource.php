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
use App\Filament\Resources\PengajuanDanaResource\Pages;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers;

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

                        Forms\Components\Select::make('tipe_pengajuan')
                            ->label('Tipe Pengajuan')
                            ->options([
                                'project' => 'Untuk Proyek',
                                'inhouse' => 'In-House (Internal)',
                            ])
                            ->live()
                            ->required()
                            ->dehydrated(false),

                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'nama_project')
                            ->searchable()
                            ->preload()
                            ->label('Pilih Proyek')
                            ->requiredIf('tipe_pengajuan', 'project')
                            ->visible(fn(Get $get) => $get('tipe_pengajuan') === 'project'),

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

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ditolak' => 'danger',
                        'Disetujui' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')->label('Dibuat oleh'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPengajuansRelationManager::class,
            RelationManagers\TransaksiPembayaransRelationManager::class,
            // \Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager::class,
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
