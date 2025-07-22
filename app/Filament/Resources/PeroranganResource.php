<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
// TAMBAHKAN IMPORT INI:
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\PeroranganResource\Pages;
use Filament\Resources\RelationManagers\RelationGroup;
use App\Filament\Resources\PeroranganResource\RelationManagers\SewaRelationManager;
use App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\CorporateRelationManager;

class PeroranganResource extends Resource
{
    protected static ?string $model = Perorangan::class;

    // Pastikan ini false agar tidak ada duplikasi menu navigasi
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        // Form untuk mengedit data Perorangan
        return $form->schema([
            Section::make('Informasi Pribadi')
                ->schema([
                    Forms\Components\TextInput::make('nama')->required(),
                    Forms\Components\TextInput::make('nik')->unique()->nullable(),
                    Forms\Components\Select::make('gender')
                        ->required()
                        ->options([
                            'Pria' => 'Pria',
                            'Wanita' => 'Wanita'
                        ]),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('telepon')->tel()->required(),
                ])->columns(2),
            Section::make('Alamat')
                ->schema([
                    Select::make('provinsi')->label('Provinsi')->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kota', null)),
                    Select::make('kota')->label('Kota/Kabupaten')->options(fn(Get $get) => $get('provinsi') ? TrefRegion::query()->where('code', 'like', $get('provinsi') . '.%')->where(DB::raw('LENGTH(code)'), 5)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('kecamatan', null)),
                    Select::make('kecamatan')->label('Kecamatan')->options(fn(Get $get) => $get('kota') ? TrefRegion::query()->where('code', 'like', $get('kota') . '.%')->where(DB::raw('LENGTH(code)'), 8)->pluck('name', 'code') : [])->live()->searchable()->afterStateUpdated(fn(Set $set) => $set('desa', null)),
                    Select::make('desa')->label('Desa/Kelurahan')->options(fn(Get $get) => $get('kecamatan') ? TrefRegion::query()->where('code', 'like', $get('kecamatan') . '.%')->where(DB::raw('LENGTH(code)'), 13)->pluck('name', 'code') : [])->live()->searchable(),
                    Textarea::make('detail_alamat')->label('Detail Alamat')->columnSpanFull(),
                ])->columns(2),
            Section::make('Dokumen Identitas')
                ->schema([
                    Forms\Components\FileUpload::make('foto_ktp')->image()->nullable(),
                    Forms\Components\FileUpload::make('foto_kk')->image()->nullable(),
                ])->columns(2),
        ]);
    }

    // Method table() di sini tidak lagi relevan karena kita tidak
    // akan menampilkan halaman index dari resource ini.
    // Bisa dihapus atau dibiarkan saja.

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Riwayat Pesanan', [
                ProjectsRelationManager::class,
                SewaRelationManager::class,
            ]),
            CorporateRelationManager::class, // Tambahkan ini untuk menampilkan relasi Corporate  
        ];
    }

    public static function getPages(): array
    {
        return [
            // Kita hanya butuh halaman 'edit' untuk menampilkan detail dan riwayat
            'edit' => Pages\EditPerorangan::route('/{record}/edit'),
            'index' => Pages\ListPerorangans::route('/'),
        ];
    }
}
