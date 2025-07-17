<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (empty($data['sewa_id'])) {
    //         $sewa = \App\Models\Sewa::create([
    //             'judul' => 'Kontrak Sewa Otomatis untuk ' . $data['nama_project'], // sesuaikan field
    //             'tgl_mulai' => now(),
    //             'tgl_selesai' => now()->addDays(7),
    //             'provinsi' => $data['provinsi'],
    //             'kota' => $data['kota'],
    //             'kecamatan' => $data['kecamatan'],
    //             'desa' => $data['desa'],
    //             'detail_alamat' => $data['detail_alamat'],
    //             'user_id' => $data['user_id'],
    //             'customer_id' => $data['customer_id'],
    //             'customer_type' => $data['customer_type'],
    //         ]);

    //         $data['sewa_id'] = $sewa->id;
    //     }

    //     return $data;
    // }
}
