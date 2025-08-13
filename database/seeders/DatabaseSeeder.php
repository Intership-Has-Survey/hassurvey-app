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
<<<<<<< Updated upstream
            // TrefRegionSeeder::class,
            PermissionSeeder::class,
=======
            TrefRegionSeeder::class,
            //PermissionSeeder::class,
>>>>>>> Stashed changes
            UserSeeder::class,
            KategoriSeeder::class,
            PemilikSeeder::class,
            CompanySeeder::class,
<<<<<<< Updated upstream
            PeroranganSeeder::class,
            CorporateSeeder::class,
            PersonelSeeder::class,
            SalesSeeder::class,
            MerkSeeder::class,
            JenisAlatSeeder::class,
            DaftarAlatSeeder::class,
=======
            //PeroranganSeeder::class,
            //CorporateSeeder::class,
            // PersonelSeeder::class,
            //SalesSeeder::class,
            //MerkSeeder::class,
            //JenisAlatSeeder::class,
            //DaftarAlatSeeder::class,
>>>>>>> Stashed changes
            BankSeeder::class,
            UserCompanySeeder::class,
            ProjectSeeder::class,
            KalibrasiSeeder::class,
            PenjualanSeeder::class,
            SewaSeeder::class,
            StatusPembayaranSeeder::class,
            PengajuanDanaSeeder::class,
        ]);
    }
}
