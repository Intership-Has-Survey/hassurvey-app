<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SewaResource\RelationManagers\RiwayatSewasRelationManager;
use App\Filament\Resources\PeroranganResource\RelationManagers\SewaRelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Perorangan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\PeroranganResource\Pages;
use Filament\Forms\Components\Section;
// TAMBAHKAN IMPORT INI:
use App\Filament\Resources\PeroranganResource\RelationManagers\ProjectsRelationManager;
use Filament\Resources\RelationManagers\RelationGroup;
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
                    Forms\Components\TextInput::make('provinsi')->required()->maxLength(2),
                    Forms\Components\TextInput::make('kota')->required()->maxLength(5),
                    Forms\Components\TextInput::make('kecamatan')->required()->maxLength(8),
                    Forms\Components\TextInput::make('desa')->required()->maxLength(13),
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