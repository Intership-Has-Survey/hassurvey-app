<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Corporate;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sewa>
 */
class SewaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // --- Logika Pengambilan Alamat ---
        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 8);
        $cityCode = substr($villageCode, 0, 5);
        $provinceCode = substr($villageCode, 0, 2);
        // --- Akhir Logika Alamat ---

        // --- Logika Tanggal ---
        $tgl_mulai = $this->faker->dateTimeBetween('-6 months', '+1 month');
        $tgl_selesai = $this->faker->dateTimeBetween($tgl_mulai, (clone $tgl_mulai)->modify('+30 days'));
        //--- Akhir Logika Tanggal ---

        return [
            'judul' => 'Sewa ' . $this->faker->randomElement(['GPS', 'Drone', 'GPS dan Drone']) . ' untuk ' . $this->faker->word(),
            'tgl_mulai' => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai,

            // Menggunakan data alamat yang valid
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Lokasi di ' . $this->faker->streetAddress(),

            'harga_perkiraan' => $this->faker->numberBetween(500000, 50000000),
            'harga_real' => $this->faker->numberBetween(500000, 50000000),
            'harga_fix' => $this->faker->numberBetween(500000, 50000000),

            'corporate_id' => Corporate::inRandomOrder()->first()->id ?? Corporate::factory(),

            // Mengambil user_id secara acak
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            
            // Add missing is_locked field
            'is_locked' => $this->faker->boolean(20), // 20% chance of being locked
        ];
    }
}
