<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Corporate; // <-- Import model Corporate

class CorporateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 25 data corporate dummy menggunakan factory.
        // Anda bisa ubah jumlahnya sesuai kebutuhan.
        Corporate::factory()->count(25)->create();
    }
}
