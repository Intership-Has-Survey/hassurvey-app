<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use App\Filament\Resources\Shared\BaseAlatSewaRelationManager;
use App\Models\Sewa;

class RiwayatSewasRelationManager extends BaseAlatSewaRelationManager
{
    protected static string $relationship = 'daftarAlat';

    protected static ?string $title = 'Alat yang Disewa';

    protected function getSewaRecord(): Sewa
    {
        return $this->getOwnerRecord();
    }
}