<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusPembayaranRelationManager;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Project';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // --- BAGIAN INFORMASI UTAMA ---
            Section::make('Informasi Proyek')
                ->schema([
                    TextInput::make('nama_project')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('kategori_id')
                        ->relationship('kategori', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Kategori Proyek')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Jenis Kategori')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('keterangan')
                                ->label('Keterangan')
                                ->required()
                                ->maxLength(300),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),

                    Select::make('sales_id')
                        ->relationship('sales', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Sales')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')->label('Nama Sales')->required(),
                            TextInput::make('telepon')->tel()->required(),
                            TextInput::make('email')->email()->required(),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),

                    DatePicker::make('tanggal_informasi_masuk')
                        ->required()
                        ->native(false),

                    Select::make('sumber')
                        ->options(['Online' => 'Online', 'Offline' => 'Offline'])
                        ->required()
                        ->native(false),
                ])->columns(2),

            // --- BAGIAN CUSTOMER (MENGGUNAKAN RELASI) ---
            Section::make('Informasi Customer')
                ->schema([
                    Select::make('customer_id')
                        ->relationship('customer', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Nama Klien/Perusahaan')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Klien/Perusahaan')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('telepon')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                            Textinput::make('alamat')
                                ->required()
                                ->columnSpanFull(),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ])
                        ->columnSpanFull(),
                    Select::make('jenis_penjualan')
                        ->options([
                            'Perseorangan' => 'Perseorangan',
                            'Corporate' => 'Corporate',
                        ])
                        ->required()
                        ->native(false)
                        ->live(),
                    TextInput::make('nama_pic')
                        ->label('Nama PIC')
                        ->visible(fn(Get $get) => $get('jenis_penjualan') === 'Corporate'),
                    Select::make('level_company')
                        ->label('Level Perusahaan')
                        ->options(['Besar' => 'Besar', 'Kecil' => 'Kecil'])
                        ->visible(fn(Get $get) => $get('jenis_penjualan') === 'Corporate')
                        ->native(false),
                    TextInput::make('lokasi')
                        ->label('Lokasi Proyek')
                        ->required(),
                    Textinput::make('alamat')
                        ->required()
                        ->columnSpanFull(),
                ]),

            // --- BAGIAN KEUANGAN & STATUS ---
            Section::make('Keuangan & Status')
                ->schema([
                    TextInput::make('nilai_project')
                        ->label('Nilai Project')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Select::make('status')
                        ->label('Status Prospek')
                        ->options(['Prospect' => 'Prospect', 'Follow up' => 'Follow up', 'Closing' => 'Closing'])
                        ->required()
                        ->native(false),

                    TextInput::make('status_pembayaran')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Select::make('status_pekerjaan_lapangan')
                        ->options([
                            'Selesai' => 'Selesai',
                            'Dalam Proses' => 'Dalam Proses',
                            'Belum Dikerjakan' => 'Belum Dikerjakan',
                        ])
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')->sortable()->searchable(),
                TextColumn::make('kategori.nama')->sortable()->searchable(),

                TextColumn::make('customer.nama')
                    ->label('Nama Klien/Perusahaan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')->sortable()->badge(),

                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('status_pekerjaan_lapangan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Dalam Proses' => 'warning',
                        'Belum Dikerjakan' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('tanggal_informasi_masuk')->label('Masuk')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PersonelsRelationManager::class,
            RelationManagers\StatusPembayaranRelationManager::class,
            RelationManagers\DaftarAlatProjectRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
