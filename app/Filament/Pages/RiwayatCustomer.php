<?php
namespace App\Filament\Pages;

use App\Models\Customer;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Filament\Resources\CustomerResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class RiwayatCustomer extends Page implements HasTable
{
    use InteractsWithTable;

    // Properti untuk navigasi dan tampilan halaman
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?string $title = 'Riwayat Customer';

    // INI YANG MEMPERBAIKI ERROR ANDA:
    // Setiap Page wajib menunjuk ke file view Blade.
    protected static string $view = 'filament.pages.riwayat-customer';

    /**
     * Mendefinisikan query utama untuk sumber data tabel.
     */
    protected function getTableQuery(): Builder
    {
        return Customer::query()->with('user');
    }

    /**
     * Mendefinisikan struktur tabel.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Klien/Perusahaan')
                    ->searchable(),
                TextColumn::make('telepon')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')->label('Editor')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('view_riwayat')
                    ->label('Lihat Riwayat')
                    ->icon('heroicon-o-eye')
                    // Arahkan ke halaman EDIT dari CustomerResource.
                    // Di halaman inilah kita akan menampilkan riwayat proyeknya.
                    ->url(fn(Customer $record): string => CustomerResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}