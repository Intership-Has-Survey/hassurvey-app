<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use App\Models\PengajuanDana;

class ConcreteTransaksiPembayaransRelationManager extends TransaksiPembayaransRelationManager
{
    protected function getPengajuanDanaRecord(): PengajuanDana
    {
        return $this->ownerRecord;
    }
}
