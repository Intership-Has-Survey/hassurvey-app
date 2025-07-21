<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use App\Filament\Resources\Shared\BaseAlatSewaRelationManager;
use App\Models\Sewa;

/**
 * Relation Manager untuk menampilkan alat yang disewa pada halaman Sewa.
 * File: app/Filament/Resources/SewaResource/RelationManagers/RiwayatSewasRelationManager.php
 */
class RiwayatSewasRelationManager extends BaseAlatSewaRelationManager
{
    protected static string $relationship = 'daftarAlat';

    protected static ?string $title = 'Alat yang Disewa';

    /**
     * Mengimplementasikan metode abstract dari parent.
     * Karena owner record di sini sudah merupakan model Sewa,
     * kita tinggal mengembalikannya saja.
     */
    protected function getSewaRecord(): Sewa
    {
        return $this->getOwnerRecord();
    }
}
