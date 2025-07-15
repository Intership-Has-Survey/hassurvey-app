<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\ProjectsRelationManager;
use App\Models\Customer;
use App\Models\TrefRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    TextInput::make('nama')->required(),
                    TextInput::make('telepon')->tel(),
                    TextInput::make('email')->email(),
                ])->columns(2),

            Forms\Components\Section::make('Alamat')
                ->schema([
                    Select::make('provinsi')
                        ->label('Provinsi')
                        ->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))
                        ->live()
                        ->searchable()
                        ->afterStateUpdated(function (Set $set) {
                            $set('kota', null);
                            $set('kecamatan', null);
                            $set('desa', null);
                        }),

                    Select::make('kota')
                        ->label('Kota/Kabupaten')
                        ->options(function (Get $get) {
                            $provinceCode = $get('province_code');
                            if (!$provinceCode) {
                                return [];
                            }
                            return TrefRegion::query()
                                ->where('code', 'like', $provinceCode . '.%')
                                ->where(DB::raw('LENGTH(code)'), 5)
                                ->pluck('name', 'code');
                        })
                        ->live()
                        ->searchable()
                        ->afterStateUpdated(function (Set $set) {
                            $set('kecamatan', null);
                            $set('desa', null);
                        }),

                    Select::make('kecamatan')
                        ->label('Kecamatan')
                        ->options(function (Get $get) {
                            $regencyCode = $get('kota');
                            if (!$regencyCode) {
                                return [];
                            }
                            return TrefRegion::query()
                                ->where('code', 'like', $regencyCode . '.%')
                                ->where(DB::raw('LENGTH(code)'), 8)
                                ->pluck('name', 'code');
                        })
                        ->live()
                        ->searchable()
                        ->afterStateUpdated(function (Set $set) {
                            $set('desa', null);
                        }),

                    Select::make('desa')
                        ->label('Desa/Kelurahan')
                        ->options(function (Get $get) {
                            $districtCode = $get('kecamatan');
                            if (!$districtCode) {
                                return [];
                            }
                            return TrefRegion::query()
                                ->where('code', 'like', $districtCode . '.%')
                                ->where(DB::raw('LENGTH(code)'), 13)
                                ->pluck('name', 'code');
                        })
                        ->live()
                        ->searchable(),

                    Textarea::make('detail_alamat')
                        ->label('Detail Alamat')
                        ->placeholder('Contoh: Jln. Merdeka No. 123, RT 01/RW 02')
                        ->columnSpanFull(),
                ])->columns(2),
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
