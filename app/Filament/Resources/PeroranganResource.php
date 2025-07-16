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
// TAMBAHKAN IMPORT INI:
use App\Filament\Resources\PeroranganResource\RelationManagers\ProjectsRelationManager;

class PeroranganResource extends Resource
{
    protected static ?string $model = Perorangan::class;

    // Pastikan ini false agar tidak ada duplikasi menu navigasi
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        // Form untuk mengedit data Perorangan
        return $form->schema([
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\Select::make('gender')
                ->required()
                ->options([
                    'pria' => 'Pria',
                    'wanita' => 'Wanita'
                ]),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('telepon')->tel()->required(),
            Forms\Components\TextInput::make('alamat')->required(),
            Forms\Components\FileUpload::make('foto_ktp')->image()->required(),
            Forms\Components\FileUpload::make('foto_kk')->image()->required(),
            // ... field lainnya
        ]);
    }

    // Method table() di sini tidak lagi relevan karena kita tidak
    // akan menampilkan halaman index dari resource ini.
    // Bisa dihapus atau dibiarkan saja.

    public static function getRelations(): array
    {
        // Daftarkan Relation Manager yang akan kita buat
        return [
            ProjectsRelationManager::class,
            SewaRelationManager::class,
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