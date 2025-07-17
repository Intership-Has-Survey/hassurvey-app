<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perorangan; // <-- Import model Perorangan

class PeroranganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 50 data perorangan dummy menggunakan factory.
        // Anda bisa ubah jumlahnya sesuai kebutuhan.
        Perorangan::factory()->count(50)->create();
    }
}
