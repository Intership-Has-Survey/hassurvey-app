<?php

namespace Database\Seeders;

use Althinect\FilamentSpatieRolesPermissions\Commands\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TrefRegionSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            UserCompanySeeder::class,
            BankSeeder::class,
            // KategoriSeeder::class,
            // PemilikSeeder::class,
            // PeroranganSeeder::class,
            // CorporateSeeder::class,
            // PersonelSeeder::class,
            // SalesSeeder::class,
            // MerkSeeder::class,
            // JenisAlatSeeder::class,
            // DaftarAlatSeeder::class,
            // ProjectSeeder::class,
            // KalibrasiSeeder::class,
            // PenjualanSeeder::class,
            // SewaSeeder::class,
            // StatusPembayaranSeeder::class,
            // PengajuanDanaSeeder::class,
        ]);
    }
}
