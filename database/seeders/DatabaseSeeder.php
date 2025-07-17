<?php

namespace Database\Seeders;

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
            KategoriSeeder::class,
            PemilikSeeder::class,
            PersonelSeeder::class,
            SalesSeeder::class,
            DaftarAlatSeeder::class,
            // CustomerSeeder::class,
            // ProjectSeeder::class,
        ]);
    }
}
