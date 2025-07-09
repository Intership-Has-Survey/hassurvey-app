<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
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
            Forms\Components\Section::make('Informasi Proyek')
                ->schema([
                    Forms\Components\TextInput::make('nama_project')
                        ->required()
                        ->columnSpanFull(),

                    // PERBAIKAN: Nama relasi menggunakan camelCase (kategori, bukan Kategori)
                    Forms\Components\Select::make('kategori_id')
                        ->relationship('kategori', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Kategori Proyek')
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nama')
                                ->label('Jenis Kategori')
                                ->required()
                                ->maxLength(50),
                        ]),

                    // PERBAIKAN: Nama relasi menggunakan camelCase (sales, bukan Sales)
                    Forms\Components\Select::make('sales_id')
                        ->relationship('sales', 'nama')
                        ->searchable()
                        ->preload()
                        ->label('Sales')
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nama')->label('Nama Sales')->required(),
                            Forms\Components\TextInput::make('telepon')->tel()->required(),
                            Forms\Components\TextInput::make('email')->email()->required(),
                        ]),

                    Forms\Components\DatePicker::make('tanggal_informasi_masuk')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('sumber')
                        ->options(['Online' => 'Online', 'Offline' => 'Offline'])
                        ->required()
                        ->native(false),
                ])->columns(2),

            // --- BAGIAN CUSTOMER (MENGGUNAKAN RELASI) ---
            Forms\Components\Section::make('Informasi Customer')
                ->schema([
                    // PERBAIKAN: Menggunakan relasi ke tabel Customer, bukan input manual
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'nama_pic')
                        ->searchable()
                        ->preload()
                        ->label('Customer (PIC)')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('nama_pic')
                                ->label('Nama Customer')
                                ->required()
                                ->maxLength(255),
                            Select::make('tipe_customer')
                                ->options([
                                    'Perorangan' => 'Perorangan',
                                    'Perusahaan' => 'Perusahaan',
                                    'Instansi Pemerintah' => 'Instansi Pemerintah',
                                ])
                                ->required()
                                ->native(false),
                            TextInput::make('nama_institusi')
                                ->label('Nama Perusahaan/Institusi')
                                ->maxLength(255)
                                ->placeholder('Kosongkan jika Perorangan'),
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
                        ])
                        ->columnSpanFull(),
                    Select::make('jenis_penjualan')
                        ->options([
                            'corporate' => 'Corporate',
                            'Perusahaan' => 'Perusahaan',
                        ]),

                    Forms\Components\TextInput::make('lokasi')
                        ->label('Lokasi Proyek')
                        ->required(),
                    Textinput::make('alamat')
                        ->required()
                        ->columnSpanFull(),
                ]),

            // --- BAGIAN KEUANGAN & STATUS ---
            Forms\Components\Section::make('Keuangan & Status')
                ->schema([
                    Forms\Components\TextInput::make('nilai_project')
                        ->label('Nilai Project')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status Prospek')
                        ->options(['Prospect' => 'Prospect', 'Follow up' => 'Follow up', 'Closing' => 'Closing'])
                        ->required()
                        ->native(false),

                    // PERBAIKAN: Dibuat non-aktif karena diisi otomatis oleh Observer
                    Forms\Components\TextInput::make('status_pembayaran')
                        ->disabled()
                        ->dehydrated(false), // Jangan simpan input dari field ini

                    Forms\Components\TextInput::make('status_pekerjaan_lapangan')
                        ->disabled()
                        ->dehydrated(false),
                ])->columns(2),

            // Mengisi user_id secara otomatis
            Forms\Components\Hidden::make('user_id')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_project')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama')->sortable()->searchable(),

                // PERBAIKAN: Menampilkan nama customer, bukan kolom lama
                Tables\Columns\TextColumn::make('customer.nama_pic')
                    ->label('Customer (PIC)')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')->sortable()->badge(),

                // PERBAIKAN: Menggunakan logika Lunas/Belum Lunas dari Observer
                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('tanggal_informasi_masuk')->label('Masuk')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            // Pastikan nama Relation Manager dan relasinya sudah benar
            RelationManagers\PersonelsRelationManager::class,
            RelationManagers\StatusPembayaranRelationManager::class,
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
