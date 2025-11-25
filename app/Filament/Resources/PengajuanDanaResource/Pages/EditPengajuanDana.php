<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use App\Filament\Resources\PengajuanDanaResource;
use App\Models\Kategori;
use App\Models\KategoriPengajuan;
use App\Models\PengajuanDana;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanDana extends EditRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['katpengajuan_id'])) {
            // dd($data);
            // dd(KategoriPengajuan::where('code', $data['katpengajuan_id'])->first());
            $data['hi'] = KategoriPengajuan::where('code', $data['katpengajuan_id'])->first()->parent_id;
        } else {
            $data['hi'] = null;
        }

        return $data;
    }
}
