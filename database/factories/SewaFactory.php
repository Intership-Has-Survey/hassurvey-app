<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Perorangan;
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
        // --- Logika untuk Relasi Polimorfik (Customer) ---
        $customerType = $this->faker->randomElement([Perorangan::class, Corporate::class]);
        $customer = $customerType::inRandomOrder()->first();
        if (!$customer) {
            $customer = $customerType::factory()->create();
        }
        // --- Akhir Logika Polimorfik ---

        // --- Logika Pengambilan Alamat ---
        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 7);
        $cityCode = substr($villageCode, 0, 4);
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

            'total_biaya' => $this->faker->numberBetween(500000, 50000000),

            // Mengisi kolom polimorfik
            'customer_id' => $customer->id,
            'customer_type' => $customerType,

            // Mengambil user_id secara acak
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}
