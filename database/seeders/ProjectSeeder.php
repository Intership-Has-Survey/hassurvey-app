<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 150 data project dummy menggunakan factory.
        // Anda bisa ubah jumlahnya sesuai kebutuhan.
        Project::factory()->count(25)->create();
    }
}
