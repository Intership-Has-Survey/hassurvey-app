<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kategori;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 8);
        $cityCode = substr($villageCode, 0, 5);
        $provinceCode = substr($villageCode, 0, 2);

        return [
            'nama_project' => 'Proyek ' . $this->faker->words(1, true),

            // Mengambil ID dari tabel relasi secara acak.
            // Pastikan seeder untuk Kategori, Sales, dan User sudah dijalankan.
            'kategori_id' => Kategori::inRandomOrder()->first()->id ?? Kategori::factory(),
            'sales_id' => Sales::inRandomOrder()->first()->id ?? Sales::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),

            'tanggal_informasi_masuk' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'sumber' => $this->faker->randomElement(['online', 'offline']),

            'nilai_project_awal' => $this->faker->numberBetween(5000000, 1000000000),

            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Lokasi di ' . $this->faker->streetAddress(),

            // Status-status yang mungkin terjadi
            'status' => $this->faker->randomElement(['Prospect', 'Follow up 1', 'Follow up 2', 'Follow up 3', 'Closing', 'Failed']),
        ];
    }
}
