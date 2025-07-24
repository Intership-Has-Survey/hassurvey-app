<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemilikResource\Pages;
use App\Filament\Resources\PemilikResource\RelationManagers;
use App\Models\Pemilik;
use App\Models\TrefRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class PemilikResource extends Resource
{
    protected static ?string $model = Pemilik::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pemilik/Investor Alat';
    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?string $pluralModelLabel = 'Pemilik Alat';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Pemilik (Sesuai KTP)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->options(['Pria' => 'Pria', 'Wanita' => 'Wanita'])
                            ->label('Jenis Kelamin')
                            ->required(),
                        Forms\Components\TextInput::make('NIK')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->unique()
                            ->validationMessages([
                                'unique' => 'NIK ini sudah terdaftar, silakan gunakan yang lain.',
                            ])
                            ->length(16)
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->unique()
                            ->validationMessages([
                                'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                            ])
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('telepon')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\Select::make('provinsi')->label('Provinsi')->required()->placeholder('Pilih Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null) && $set('kecamatan', null) && $set('desa', null)),
                        Forms\Components\Select::make('kota')->label('Kota/Kabupaten')->required()->placeholder('Pilih Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null) && $set('desa', null)),
                        Forms\Components\Select::make('kecamatan')->label('Kecamatan')->required()->placeholder('Pilih Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                        Forms\Components\Select::make('desa')->label('Desa/Kelurahan')->required()->placeholder('Pilih Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                        Forms\Components\Textarea::make('detail_alamat')->label('Detail Alamat')->required()->columnSpanFull()->placeholder('cth: Jl. Supriyadi No,12, RT.3/RW.4'),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pendapatan & Bagi Hasil')
                    ->schema([
                        Forms\Components\TextInput::make('persen_bagihasil')
                            ->label('Persentase Bagi Hasil (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(20)
                            ->postfix('%')
                            ->required(),

                        // --- PERBAIKAN UTAMA DI SINI ---
                        // Menggunakan callback function untuk menghitung dan menampilkan data secara dinamis.
                        Forms\Components\Placeholder::make('total_pendapatanktr')
                            ->label('Total Pendapatan Kotor')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                // Pastikan relasi 'riwayatSewaAlat' ada di model Pemilik
                                $total = $record->riwayatSewaAlat()->sum('biaya_sewa_alat');
                                return Number::currency($total, 'IDR');
                            }),

                        Forms\Components\Placeholder::make('total_pendapataninv')
                            ->label('Total Pendapatan Investor/Pemilik')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                $total = $record->riwayatSewaAlat()->sum('pendapataninv');
                                return Number::currency($total, 'IDR');
                            }),

                        Forms\Components\Placeholder::make('total_pendapatanhas')
                            ->label('Total Pendapatan untuk Has Survey')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                $total = $record->riwayatSewaAlat()->sum('pendapatanhas');
                                return Number::currency($total, 'IDR');
                            }),

                    ])->columns(1)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Pemilik')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('NIK')->label('NIK')->searchable(),
                Tables\Columns\TextColumn::make('telepon')->label('No. Telepon')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tanggal Dibuat')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pemilik/Investor Alat yang Terdaftar')
            ->emptyStateDescription('Silahkan buat data pemilik/investor baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DaftarAlatRelationManager::class,
            RelationManagers\RiwayatSewaPemilikRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        // Kode ini sudah benar, tidak perlu diubah.
        // Anda bisa menghapus file EditPemilik.php jika tidak ada logika lain di dalamnya.
        return [
            'index' => Pages\ListPemiliks::route('/'),
            'create' => Pages\CreatePemilik::route('/create'),
            'edit' => Pages\EditPemilik::route('/{record}/edit'),
        ];
    }
}
