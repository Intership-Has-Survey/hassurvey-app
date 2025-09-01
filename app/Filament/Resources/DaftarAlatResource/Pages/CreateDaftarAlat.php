<?php

namespace App\Filament\Resources\DaftarAlatResource\Pages;

use App\Filament\Resources\DaftarAlatResource;
use Filament\Actions\Action; // <-- PERUBAHAN DI SINI
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;

class CreateDaftarAlat extends CreateRecord
{
    protected static string $resource = DaftarAlatResource::class;

    protected static ?string $title = 'Tambah Alat';

    public function getBreadcrumb(): string
    {
        return 'Buat';
    }
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan'),
            ...(static::canCreateAnother()
                ? [$this->getCreateAnotherFormAction()->label('Simpan & tambah lagi')]
                : []),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // session(['nama' => 'Syahrial']);
    //     // $data['company_id'] = session('company_id');
    //     // dd(auth()->user()->companies()->company_id); // atau tenant()->id jika pakai package
    //     $currentUrl = request();           // http://127.0.0.1:8000/admin/xxx/create
    //     // $slug = request()->segment(2);                // admin
    //     // $uuid = request()->segment(3);
    //     dd($currentUrl); // atau tenant()->id jika pakai package

    //     return $data;
    // }
}
