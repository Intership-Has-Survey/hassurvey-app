<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class TrefRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file .sql 
        $sqlFilePath = database_path('seeders\sql\tref_regions.sql');

       
        DB::unprepared(file_get_contents($sqlFilePath));
    }
}
