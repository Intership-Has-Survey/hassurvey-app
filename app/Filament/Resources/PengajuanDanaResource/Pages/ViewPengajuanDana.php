<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use Filament\Actions;
use App\Models\KategoriPengajuan;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\PengajuanDanaResource;

class ViewPengajuanDana extends ViewRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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
