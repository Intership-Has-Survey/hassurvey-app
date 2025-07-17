<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB; // <-- Import DB facade
use App\Models\User; // <-- Import model User

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Corporate>
 */
class CorporateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Ambil satu data desa secara acak dari tabel 'villages'.
        // Pastikan nama tabel 'villages' sudah benar.
        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();

        // Jika data wilayah kosong, hentikan proses untuk menghindari error.
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }

        // 2. Pecah kodenya untuk mendapatkan ID induk.
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 7);
        $cityCode = substr($villageCode, 0, 4);
        $provinceCode = substr($villageCode, 0, 2);

        return [
            'nama' => $this->faker->company(),
            'nib' => $this->faker->unique()->numerify('#############'), // NIB biasanya 13 digit
            'level' => $this->faker->randomElement(['Besar', 'Menengah', 'Kecil']),
            'email' => $this->faker->unique()->companyEmail(),
            'telepon' => $this->faker->phoneNumber(),

            // ⚙️ Gunakan kode wilayah yang sudah kita dapatkan
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Jl. ' . $this->faker->streetName() . ' No. ' . $this->faker->buildingNumber(),

            // Mengambil user_id secara acak dari tabel users yang sudah ada
            // atau membuat user baru jika tabel user kosong.
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}
