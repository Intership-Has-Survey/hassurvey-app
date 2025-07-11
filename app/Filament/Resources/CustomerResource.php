<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\CustomerResource\Pages;
// TAMBAHKAN IMPORT INI:
use App\Filament\Resources\CustomerResource\RelationManagers\ProjectsRelationManager;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    // Pastikan ini false agar tidak ada duplikasi menu navigasi
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        // Form untuk mengedit data customer
        return $form->schema([
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('telepon')->tel(),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('alamat'),

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
        ];
    }

    public static function getPages(): array
    {
        return [
            // Kita hanya butuh halaman 'edit' untuk menampilkan detail dan riwayat
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
}
